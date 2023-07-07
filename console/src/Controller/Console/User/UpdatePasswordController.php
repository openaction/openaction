<?php

namespace App\Controller\Console\User;

use App\Controller\AbstractController;
use App\Form\User\ChangePasswordType;
use App\Form\User\Model\ChangePasswordData;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/console/user/password')]
class UpdatePasswordController extends AbstractController
{
    #[Route('/update', name: 'console_user_password', methods: ['GET', 'POST'])]
    public function updatePassword(UserRepository $repo, UserPasswordHasherInterface $hasher, TranslatorInterface $translator, Request $request)
    {
        $data = new ChangePasswordData();

        $form = $this->createForm(ChangePasswordType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repo->upgradePassword($this->getUser(), $hasher->hashPassword($this->getUser(), $data->newPassword));

            $this->addFlash('success', 'user.password_change_success');

            return $this->redirectToRoute('console_user_password');
        }

        return $this->render('console/user/password/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
