<?php

namespace App\Controller\Console\Project\Developers;

use App\Controller\AbstractController;
use App\Entity\Website\Redirection;
use App\Form\Developer\Model\RedirectionData;
use App\Form\Developer\RedirectionType;
use App\Platform\Permissions;
use App\Repository\Website\RedirectionRepository;
use App\Util\Json;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/developers/redirections')]
class RedirectionController extends AbstractController
{
    private RedirectionRepository $repository;
    private EntityManagerInterface $em;

    public function __construct(RedirectionRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    #[Route('', name: 'console_developers_redirections')]
    public function index()
    {
        $this->denyAccessUnlessGranted(Permissions::PROJECT_DEVELOPER_REDIRECTIONS, $this->getProject());
        $this->denyIfSubscriptionExpired();

        return $this->render('console/project/developers/redirections/index.html.twig', [
            'redirections' => $this->repository->getProjectRedirections($this->getProject()),
        ]);
    }

    #[Route('/sort', name: 'console_developers_redirections_sort')]
    public function sort(Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::PROJECT_DEVELOPER_REDIRECTIONS, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();

        $data = Json::decode($request->request->get('data'));

        if (0 === count($data)) {
            throw new BadRequestHttpException('Invalid payload sort');
        }

        try {
            $this->repository->sort($data);
        } catch (\InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        return new JsonResponse(['success' => 1]);
    }

    #[Route('/create', name: 'console_developers_redirections_create')]
    public function create(Request $request)
    {
        $item = new Redirection($this->getProject(), '', '', 302,
            1 + $this->repository->count(['project' => $this->getProject()])
        );

        return $this->createOrEdit($item, $request, 'create.html.twig');
    }

    #[Route('/edit/{id}', name: 'console_developers_redirections_edit')]
    public function edit(Redirection $item, Request $request)
    {
        return $this->createOrEdit($item, $request, 'edit.html.twig');
    }

    #[Route('/delete/{id}', name: 'console_developers_redirections_delete')]
    public function delete(Redirection $item, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::PROJECT_DEVELOPER_REDIRECTIONS, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();

        $this->em->remove($item);
        $this->em->flush();

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute('console_developers_redirections', ['projectUuid' => $this->getProject()->getUuid()]);
    }

    private function createOrEdit(Redirection $item, Request $request, string $template)
    {
        $this->denyAccessUnlessGranted(Permissions::PROJECT_DEVELOPER_REDIRECTIONS, $this->getProject());
        $this->denyIfSubscriptionExpired();

        $data = RedirectionData::createFromRedirection($item);

        $form = $this->createForm(RedirectionType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item->applyDataUpdate($data);

            $this->em->persist($item);
            $this->em->flush();

            $this->addFlash('success', 'configuration.updated_success');

            return $this->redirectToRoute('console_developers_redirections', ['projectUuid' => $this->getProject()->getUuid()]);
        }

        return $this->render('console/project/developers/redirections/'.$template, [
            'form' => $form->createView(),
            'item' => $item,
        ]);
    }
}
