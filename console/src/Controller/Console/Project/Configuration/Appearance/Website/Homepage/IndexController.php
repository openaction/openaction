<?php

namespace App\Controller\Console\Project\Configuration\Appearance\Website\Homepage;

use App\Controller\AbstractController;
use App\Entity\Website\MenuItem;
use App\Platform\Permissions;
use App\Repository\Website\MenuItemRepository;
use App\Repository\Website\PageBlockRepository;
use App\Util\Json;
use App\Website\PageBlock\BlockInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/configuration/appearance/website/homepage')]
class IndexController extends AbstractController
{
    private PageBlockRepository $repository;

    public function __construct(PageBlockRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route('', name: 'console_configuration_appearance_website_homepage')]
    public function index(MenuItemRepository $menuRepo)
    {
        $this->denyAccessUnlessGranted(Permissions::PROJECT_CONFIG_APPEARANCE, $this->getProject());
        $this->denyIfSubscriptionExpired();

        return $this->render('console/project/configuration/appearance/website/homepage/index.html.twig', [
            'header' => $menuRepo->getProjectMenuTree($this->getProject(), MenuItem::POSITION_HEADER),
            'home_blocks' => $this->repository->getProjectBlocks($this->getProject(), BlockInterface::PAGE_HOME),
        ]);
    }

    #[Route('/sort', name: 'console_configuration_appearance_website_homepage_sort')]
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
}
