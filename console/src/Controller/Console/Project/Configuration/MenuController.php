<?php

namespace App\Controller\Console\Project\Configuration;

use App\Controller\AbstractController;
use App\Entity\Website\MenuItem;
use App\Form\Appearance\Model\WebsiteMenuItemData;
use App\Form\Appearance\WebsiteMenuItemType;
use App\Platform\Permissions;
use App\Repository\Website\MenuItemRepository;
use App\Util\Json;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/configuration/menu')]
class MenuController extends AbstractController
{
    private MenuItemRepository $repository;
    private EntityManagerInterface $em;

    public function __construct(MenuItemRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    #[Route('', name: 'console_configuration_menu')]
    public function index()
    {
        $this->denyAccessUnlessGranted(Permissions::PROJECT_CONFIG_APPEARANCE, $this->getProject());
        $this->denyIfSubscriptionExpired();

        return $this->render('console/project/configuration/menu/index.html.twig', [
            'header' => $this->repository->getProjectMenuTree($this->getProject(), MenuItem::POSITION_HEADER),
            'footer' => $this->repository->getProjectMenuTree($this->getProject(), MenuItem::POSITION_FOOTER),
        ]);
    }

    #[Route('/sort/{position}', name: 'console_configuration_menu_sort')]
    public function sort(Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::PROJECT_CONFIG_APPEARANCE, $this->getProject());
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

    #[Route('/create/{position}', name: 'console_configuration_menu_create')]
    public function create(Request $request, string $position)
    {
        $item = new MenuItem($this->getProject(), $position, '', '',
            1 + $this->repository->count(['project' => $this->getProject(), 'position' => $position])
        );

        return $this->createOrEdit($item, $request, 'create.html.twig');
    }

    #[Route('/edit/{id}', name: 'console_configuration_menu_edit')]
    public function edit(MenuItem $item, Request $request)
    {
        return $this->createOrEdit($item, $request, 'edit.html.twig');
    }

    #[Route('/delete/{id}', name: 'console_configuration_menu_delete')]
    public function delete(MenuItem $item, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::PROJECT_CONFIG_APPEARANCE, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();

        $this->em->remove($item);
        $this->em->flush();

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute('console_configuration_menu', ['projectUuid' => $this->getProject()->getUuid()]);
    }

    private function createOrEdit(MenuItem $item, Request $request, string $template)
    {
        $this->denyAccessUnlessGranted(Permissions::PROJECT_CONFIG_APPEARANCE, $this->getProject());
        $this->denyIfSubscriptionExpired();

        $data = WebsiteMenuItemData::createFromMenuItem($item);

        $form = $this->createForm(WebsiteMenuItemType::class, $data, [
            'project' => $this->getProject(),
            'position' => $item->getPosition(),
            'current_id' => $item->getId(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item->applyDataUpdate($data);

            $this->em->persist($item);
            $this->em->flush();

            $this->addFlash('success', 'configuration.updated_success');

            return $this->redirectToRoute('console_configuration_menu', ['projectUuid' => $this->getProject()->getUuid()]);
        }

        return $this->render('console/project/configuration/menu/'.$template, [
            'form' => $form->createView(),
            'item' => $item,
        ]);
    }
}
