<?php

namespace App\Controller\Api;

use App\Api\ApiRequestQueryParser;
use App\Api\Payload\PayloadValidationException;
use App\Api\Transformer\Validator\ConstraintViolationTransformer;
use App\Controller\Util\ApiControllerTrait;
use Doctrine\ORM\Tools\Pagination\Paginator;
use League\Fractal\Manager;
use League\Fractal\Pagination\DoctrinePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\TransformerAbstract;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractApiController extends BaseController
{
    use ApiControllerTrait;

    protected ApiRequestQueryParser $apiQueryParser;
    protected ConstraintViolationTransformer $constraintViolationTransformer;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function denyUnlessSameOrganization($entity)
    {
        if (!$entity) {
            throw $this->createNotFoundException('Entity not found, current organization check couldn\'t be done.');
        }

        if (!method_exists($entity, 'getOrganization')) {
            throw new \LogicException(sprintf('Entity %s of type %s has no getOrganization() method.', $entity->getId(), get_class($entity)));
        }

        if ($entity->getOrganization()->getId() !== $this->getUser()->getOrganization()->getId()) {
            throw $this->createAccessDeniedException('Invalid organization for current entity.');
        }
    }

    public function denyUnlessSameProject($entity)
    {
        if (!$entity) {
            throw $this->createNotFoundException('Entity not found, current project check couldn\'t be done.');
        }

        if (!method_exists($entity, 'getProject')) {
            throw new \LogicException(sprintf('Entity %s of type %s has no getProject() method.', $entity->getId(), get_class($entity)));
        }

        if ($entity->getProject()->getId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException('Invalid project for current entity.');
        }
    }

    public function denyUnlessToolEnabled(string $tool)
    {
        if (!$this->getUser()->isToolEnabled($tool)) {
            throw $this->createNotFoundException('Tool not enabled on project');
        }
    }

    protected function handleApiConstraintViolations(ConstraintViolationListInterface $list): Response
    {
        $response = $this->handleApiCollection($list, $this->constraintViolationTransformer, false);
        $response->setStatusCode(Response::HTTP_BAD_REQUEST);

        return $response;
    }

    protected function handleApiCollection(iterable $data, TransformerAbstract $transformer, bool $paginate): Response
    {
        $resource = new Collection($data, $transformer);

        if ($paginate) {
            if (!$data instanceof Paginator) {
                throw new \InvalidArgumentException('Providing a Doctrine Paginator is required when using pagination.');
            }

            $request = $this->container->get('request_stack')->getCurrentRequest();
            $router = $this->container->get('router');
            $route = $request->attributes->get('_route');
            $params = array_merge($request->query->all(), $request->attributes->get('_route_params'));

            $resource->setPaginator(
                new DoctrinePaginatorAdapter($data, static function (int $page) use ($router, $route, $params) {
                    $params['page'] = $page;

                    return $router->generate($route, $params, UrlGeneratorInterface::ABSOLUTE_URL);
                })
            );
        }

        return new JsonResponse($this->createFractalManager()->createData($resource)->toArray());
    }

    protected function handleApiItem($item, TransformerAbstract $transformer, int $status = 200): Response
    {
        return new JsonResponse(
            $this->createFractalManager()->createData(new Item($item, $transformer))->toArray(),
            $status,
        );
    }

    private function createFractalManager(): Manager
    {
        $fractal = new Manager();
        $fractal->parseIncludes($this->apiQueryParser->getIncludes());
        $fractal->parseExcludes($this->apiQueryParser->getExcludes());
        $fractal->setSerializer(new ArraySerializer());

        return $fractal;
    }

    /**
     * @template T
     *
     * @param class-string<T> $payloadType
     *
     * @return T
     */
    public function createPayloadFromRequestContent(Request $request, string $payloadType)
    {
        $payload = $this->serializer->deserialize(
            data: $request->getContent(),
            type: $payloadType,
            format: 'json'
        );

        $errors = $this->validator->validate($payload);
        if ($errors->count() > 0) {
            throw new PayloadValidationException($errors);
        }

        return $payload;
    }

    /**
     * @template T
     *
     * @param class-string<T> $payloadType
     *
     * @return T
     */
    public function createPayloadFromRequestFiles(Request $request, string $payloadType)
    {
        $payload = new $payloadType();

        $infos = new PropertyInfoExtractor([new ReflectionExtractor()]);

        $accessor = new PropertyAccessor();
        foreach ($infos->getProperties($payloadType) as $propertyName) {
            $accessor->setValue($payload, $propertyName, $request->files->get($propertyName));
        }

        $errors = $this->validator->validate($payload);
        if ($errors->count() > 0) {
            throw new PayloadValidationException($errors);
        }

        return $payload;
    }

    #[Required]
    public function setQueryParser(ApiRequestQueryParser $queryParser): void
    {
        $this->apiQueryParser = $queryParser;
    }

    #[Required]
    public function setConstraintViolationTransformer(ConstraintViolationTransformer $transformer): void
    {
        $this->constraintViolationTransformer = $transformer;
    }

    #[Required]
    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }

    #[Required]
    public function setValidator(ValidatorInterface $validator): void
    {
        $this->validator = $validator;
    }
}
