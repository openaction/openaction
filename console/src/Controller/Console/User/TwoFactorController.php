<?php

namespace App\Controller\Console\User;

use App\Controller\AbstractController;
use App\Form\User\ConfirmPasswordType;
use App\Form\User\Model\TwoFactorData;
use App\Form\User\TwoFactorType;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticatorInterface;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/user/two-factor')]
class TwoFactorController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('', name: 'console_user_2fa', methods: ['GET'])]
    public function status(Request $request)
    {
        return $this->render('console/user/two_factor/status.html.twig', [
            'wasForced' => $request->query->getBoolean('forced'),
        ]);
    }

    #[Route('/confirm-password', name: 'console_user_2fa_confirm_password', methods: ['GET', 'POST'])]
    public function confirmPassword(TotpAuthenticatorInterface $totp, Request $request)
    {
        $user = $this->getUser();
        if ($user->isTwoFactorEnabled()) {
            return $this->redirectToRoute('console_user_2fa');
        }

        $form = $this->createForm(ConfirmPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->startTwoFactorEnablingProcess($totp->generateSecret());

            $this->em->persist($user);
            $this->em->flush();

            return $this->redirectToRoute('console_user_2fa_enable');
        }

        return $this->render('console/user/two_factor/password_confirm.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/enable', name: 'console_user_2fa_enable', methods: ['GET', 'POST'])]
    public function enable(TotpAuthenticatorInterface $totp, Request $request)
    {
        $user = $this->getUser();
        if (!$user->isInTwoFactorEnablingProcess()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(TwoFactorType::class, new TwoFactorData($totp, $user));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->finishEnablingTwoFactor();

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', 'user.settings_update_success');

            return $this->redirectToRoute('console_user_2fa_enabled');
        }

        return $this->render('console/user/two_factor/enable.html.twig', [
            'form' => $form->createView(),
            'qrcode' => $totp->getQRContent($user),
        ]);
    }

    #[Route('/qr-code', name: 'console_user_2fa_qr_code', methods: ['GET', 'POST'])]
    public function qrCode(TotpAuthenticatorInterface $totpAuthenticator)
    {
        $user = $this->getUser();
        if (!$user->isInTwoFactorEnablingProcess()) {
            throw $this->createNotFoundException();
        }

        $qrCode = QrCode::create($totpAuthenticator->getQRContent($user));
        $qrCode->setSize(400);
        $qrCode->setMargin(10);
        $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevelLow());

        return new Response((new PngWriter())->write($qrCode)->getString(), 200, ['Content-Type' => 'image/png']);
    }

    #[Route('/enabled', name: 'console_user_2fa_enabled', methods: ['GET'])]
    public function enabled()
    {
        return $this->render('console/user/two_factor/enabled.html.twig');
    }

    #[Route('/download-backup-codes', name: 'console_user_2fa_download_backups', methods: ['GET'])]
    public function downloadBackupCodes()
    {
        $user = $this->getUser();
        if (0 === count($user->getTwoFactorBackupCodes())) {
            throw $this->createNotFoundException();
        }

        $response = new Response(implode("\n", $user->getTwoFactorBackupCodes()));
        $disposition = HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, 'recovery_codes.txt');

        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    #[Route('/disable', name: 'console_user_2fa_disable', methods: ['GET'])]
    public function disable(Request $request)
    {
        $this->denyUnlessValidCsrf($request);

        $user = $this->getUser();
        if (!$user->isTwoFactorEnabled()) {
            throw $this->createNotFoundException();
        }

        $user->disableTwoFactor();

        $this->em->persist($user);
        $this->em->flush();

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['redirect' => $this->generateUrl('console_user_2fa')]);
        }

        return $this->redirectToRoute('console_user_2fa');
    }
}
