<?php

namespace App\Controller\Console\Project\Developers;

use App\Controller\AbstractController;
use App\Form\Developer\Model\UpdateCaptchaData;
use App\Form\Developer\UpdateCaptchaType;
use App\Platform\Permissions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/project/{projectUuid}/developers/captcha')]
class CaptchaController extends AbstractController
{
    #[Route('', name: 'console_developers_captcha')]
    public function index(EntityManagerInterface $em, Request $request)
    {
        $this->denyAccessUnlessGranted(Permissions::PROJECT_DEVELOPER_ACCESS, $this->getProject());
        $this->denyIfSubscriptionExpired();
        $this->requireTwoFactorAuthIfForced();

        $data = UpdateCaptchaData::createFromProject($this->getProject());

        $form = $this->createForm(UpdateCaptchaType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getProject()->applyWebsiteTurnstileUpdate($data);

            $em->persist($this->getProject());
            $em->flush();

            $this->addFlash('success', 'configuration.updated_success');

            return $this->redirectToRoute('console_developers_captcha', ['projectUuid' => $this->getProject()->getUuid()]);
        }

        return $this->render('console/project/developers/captcha.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
