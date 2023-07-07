<?php

namespace App\Controller\Console\Project\Configuration\Appearance\Website\Homepage;

use App\Controller\AbstractController;
use App\Entity\Website\PageBlock;
use App\Platform\Permissions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/configuration/appearance/website/homepage')]
class DeleteController extends AbstractController
{
    #[Route('/block/{id}/delete', name: 'console_configuration_appearance_website_homepage_block_delete')]
    public function delete(EntityManagerInterface $manager, PageBlock $block, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::WEBSITE_PAGES_MANAGE, $this->getProject());
        $this->denyUnlessValidCsrf($request);
        $this->denyIfSubscriptionExpired();
        $this->denyUnlessSameProject($block);

        $manager->remove($block);
        $manager->flush();

        if ($request->headers->has('X-Ajax-Confirm')) {
            return new JsonResponse(['success' => true]);
        }

        return $this->redirectToRoute('console_configuration_appearance_website_homepage', ['projectUuid' => $this->getProject()->getUuid()]);
    }
}
