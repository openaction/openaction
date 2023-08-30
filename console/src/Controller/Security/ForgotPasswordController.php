<?php

namespace App\Controller\Security;

use App\Form\User\ForgotPasswordType;
use App\Form\User\Model\ForgotPasswordData;
use App\Form\User\Model\ResetPasswordData;
use App\Form\User\ResetPasswordType;
use App\Mailer\PlatformMailer;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/security')]
class ForgotPasswordController extends AbstractController
{
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route('/forgot-password-request', name: 'security_forgot_password_request', methods: ['GET', 'POST'])]
    public function request(EntityManagerInterface $em, PlatformMailer $mailer, Request $request)
    {
        $data = new ForgotPasswordData();

        $form = $this->createForm(ForgotPasswordType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->repository->findOneByEmail($data->email);

            if ($user && (!$user->getDueDateResetPassword() || $user->isDueDateResetPasswordExpired())) {
                $user->createForgotPasswordSecret();

                $em->persist($user);
                $em->flush();

                $mailer->sendForgottenPasswordRequest($user);
            }

            return $this->redirectToRoute('security_forgot_password_request_sent');
        }

        return $this->render('security/forgot-password/request_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/forgot-password-request-sent', name: 'security_forgot_password_request_sent', methods: ['GET'])]
    public function sent()
    {
        return $this->render('security/forgot-password/request_sent.html.twig');
    }

    #[Route('/forgot-password/{uuid}', name: 'security_forgot_password', methods: ['GET', 'POST'])]
    public function reset(UserPasswordHasherInterface $hasher, PlatformMailer $mailer, Request $request, string $uuid)
    {
        if (!$user = $this->repository->findOneBy(['secretResetPassword' => $uuid])) {
            throw $this->createNotFoundException();
        }

        if ($user->isDueDateResetPasswordExpired()) {
            throw $this->createNotFoundException();
        }

        $data = new ResetPasswordData();

        $form = $this->createForm(ResetPasswordType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->clearForgotPasswordSecret();
            $this->repository->upgradePassword($user, $hasher->hashPassword($user, $data->newPassword));
            $mailer->sendPasswordUpdated($user);

            return $this->redirectToRoute('security_login', ['reset' => 1]);
        }

        return $this->render('security/forgot-password/reset_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
