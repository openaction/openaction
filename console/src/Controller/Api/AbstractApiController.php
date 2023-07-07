<?php

namespace App\Controller\Api;

use App\Api\ApiRequestQueryParser;
use App\Api\Transformer\Validator\ConstraintViolationTransformer;
use App\Controller\Util\ApiControllerTrait;
use App\Entity\Organization;
use App\Entity\Project;
use Doctrine\ORM\Tools\Pagination\Paginator;
use League\Fractal\Manager;
use League\Fractal\Pagination\DoctrinePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\TransformerAbstract;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class AbstractApiController extends BaseController
{
    use ApiControllerTrait;

    protected ApiRequestQueryParser $apiQueryParser;
    protected ConstraintViolationTransformer $constraintViolationTransformer;

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

    protected function handleApiItem($item, TransformerAbstract $transformer): Response
    {
        return new JsonResponse($this->createFractalManager()->createData(new Item($item, $transformer))->toArray());
    }

    private function createFractalManager(): Manager
    {
        $fractal = new Manager();
        $fractal->parseIncludes($this->apiQueryParser->getIncludes());
        $fractal->parseExcludes($this->apiQueryParser->getExcludes());
        $fractal->setSerializer(new ArraySerializer());

        return $fractal;
    }

    public function getOrganization(): ?Organization
    {
        return $this->container->get('request_stack')->getCurrentRequest()->attributes->get('organization');
    }

    public function getProject(): ?Project
    {
        return $this->container->get('request_stack')->getCurrentRequest()->attributes->get('project');
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setQueryParser(ApiRequestQueryParser $queryParser)
    {
        $this->apiQueryParser = $queryParser;
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setConstraintViolationTransformer(ConstraintViolationTransformer $transformer)
    {
        $this->constraintViolationTransformer = $transformer;
    }
}
