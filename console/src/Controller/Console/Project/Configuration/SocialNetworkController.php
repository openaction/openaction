<?php

namespace App\Controller\Console\Project\Configuration;

use App\Cdn\CdnUploader;
use App\Cdn\Model\CdnUploadRequest;
use App\Controller\AbstractController;
use App\Entity\Model\SocialSharers;
use App\Form\Project\Model\UpdateMetasData;
use App\Form\Project\Model\UpdateSocialAccountsData;
use App\Form\Project\Model\UpdateSocialSharersData;
use App\Form\Project\UpdateMetasType;
use App\Form\Project\UpdateSocialAccountsType;
use App\Form\Project\UpdateSocialSharersType;
use App\Platform\Permissions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/configuration/social-networks')]
class SocialNetworkController extends AbstractController
{
    #[Route('/metas', name: 'console_configuration_social_networks_metas')]
    public function metas(CdnUploader $uploader, EntityManagerInterface $manager, Request $request)
    {
        $project = $this->getProject();

        $this->denyAccessUnlessGranted(Permissions::PROJECT_CONFIG_SOCIALS, $project);
        $this->denyIfSubscriptionExpired();
        $this->requireTwoFactorAuthIfForced();

        $data = UpdateMetasData::createFromProject($project);

        $form = $this->createForm(UpdateMetasType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->applyMetasUpdate($data);
            $manager->persist($project);

            if ($data->websiteSharer) {
                $toRemove = $project->getWebsiteSharer();

                $project->setWebsiteSharer($uploader->upload(
                    CdnUploadRequest::createProjectSharerRequest($project, $data->websiteSharer)
                ));

                $manager->persist($project);

                if ($toRemove) {
                    $manager->remove($toRemove);
                }
            }

            $manager->flush();

            $this->addFlash('success', 'configuration.appearance_success');

            return $this->redirectToRoute('console_configuration_social_networks_metas', [
                'projectUuid' => $project->getUuid(),
            ]);
        }

        return $this->render('console/project/configuration/socials/metas.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/accounts', name: 'console_configuration_social_networks_accounts')]
    public function accounts(EntityManagerInterface $manager, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::PROJECT_CONFIG_SOCIALS, $this->getProject());
        $this->denyIfSubscriptionExpired();

        $project = $this->getProject();

        $data = UpdateSocialAccountsData::createFromProject($project);
        $form = $this->createForm(UpdateSocialAccountsType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->applySocialsAccountsUpdate($data);

            $manager->persist($project);
            $manager->flush();

            $this->addFlash('success', 'configuration.socials_success');

            return $this->redirectToRoute('console_configuration_social_networks_accounts', [
                'projectUuid' => $project->getUuid(),
            ]);
        }

        return $this->render('console/project/configuration/socials/accounts.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/sharers', name: 'console_configuration_social_networks_sharers')]
    public function shares(EntityManagerInterface $manager, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::PROJECT_CONFIG_SOCIALS, $this->getProject());
        $this->denyIfSubscriptionExpired();

        $project = $this->getProject();

        $data = new UpdateSocialSharersData($project->getSocialSharers());

        $form = $this->createForm(UpdateSocialSharersType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->applySocialSharersUpdate(new SocialSharers($data->sharers));

            $manager->persist($project);
            $manager->flush();

            $this->addFlash('success', 'configuration.socials_success');

            return $this->redirectToRoute('console_configuration_social_networks_sharers', [
                'projectUuid' => $project->getUuid(),
            ]);
        }

        return $this->render('console/project/configuration/socials/sharers.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
