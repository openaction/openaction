<?php

namespace App\Controller\Console\Project\Developers;

use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Controller\AbstractController;
use App\Entity\Theme\ProjectAsset;
use App\Form\Theme\AssetType;
use App\Form\Theme\Model\AssetData;
use App\Platform\Permissions;
use App\Repository\Theme\ProjectAssetRepository;
use App\Theme\ThemeManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/developers/theme')]
class ThemeController extends AbstractController
{
    private ThemeManager $themeManager;

    public function __construct(ThemeManager $themeManager)
    {
        $this->themeManager = $themeManager;
    }

    #[Route('', name: 'console_developers_theme')]
    public function index(ProjectAssetRepository $assetRepository)
    {
        $this->denyAccessUnlessGranted(Permissions::PROJECT_DEVELOPER_THEME, $this->getProject());
        $this->denyIfSubscriptionExpired();

        return $this->render('console/project/developers/theme.html.twig', [
            'files' => $this->themeManager->getThemeFiles($this->getProject()),
            'assets' => $assetRepository->findBy(['project' => $this->getProject()], ['name' => 'ASC']),
            'addAssetForm' => $this->createForm(AssetType::class, new AssetData())->createView(),
        ]);
    }

    #[Route('/save', name: 'console_developers_theme_save', methods: ['POST'])]
    public function save(EntityManagerInterface $em, Request $request)
    {
        $project = $this->getProject();
        $this->denyAccessUnlessGranted(Permissions::PROJECT_DEVELOPER_THEME, $project);
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();

        $this->themeManager->applyThemeChanges($project, $request);

        $em->persist($project);
        $em->flush();

        $this->addFlash('success', 'configuration.appearance_success');

        return $this->redirectToRoute('console_developers_theme', ['projectUuid' => $project->getUuid()]);
    }

    #[Route('/asset/add', name: 'console_developers_theme_asset_add')]
    public function assetAdd(CdnUploader $uploader, EntityManagerInterface $em, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::PROJECT_DEVELOPER_THEME, $this->getProject());
        $this->denyIfSubscriptionExpired();

        $data = new AssetData();

        $form = $this->createForm(AssetType::class, $data);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->redirectToRoute('console_developers_theme', ['projectUuid' => $this->getProject()->getUuid()]);
        }

        $upload = $uploader->upload(CdnUploadRequest::createProjectAssetRequest($this->getProject(), $data->file));

        $em->persist(ProjectAsset::createFromUpload($this->getProject(), $data->file, $upload));
        $em->flush();

        $this->addFlash('success', 'configuration.assets_success');

        return $this->redirectToRoute('console_developers_theme', ['projectUuid' => $this->getProject()->getUuid()]);
    }

    #[Route('/asset/{uuid}/remove', name: 'console_developers_theme_asset_remove')]
    public function assetRemove(EntityManagerInterface $em, ProjectAsset $asset, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::PROJECT_DEVELOPER_THEME, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessValidCsrf($request);

        $em->remove($asset);
        $em->flush();

        $this->addFlash('success', 'configuration.assets_success');

        return $this->redirectToRoute('console_developers_theme', ['projectUuid' => $this->getProject()->getUuid()]);
    }
}
