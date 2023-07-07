<?php

namespace App\Controller\Console\User;

use App\Controller\AbstractController;
use App\Form\User\AccountType;
use App\Form\User\Model\AccountData;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/user/account')]
class AccountController extends AbstractController
{
    #[Route('', name: 'console_user_account_update', methods: ['GET', 'POST'])]
    public function update(EntityManagerInterface $manager, RequestStack $requestStack, Request $request)
    {
        $data = AccountData::createFromUser($this->getUser());

        $form = $this->createForm(AccountType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $user->applyAccountUpdate($data);

            $manager->persist($user);
            $manager->flush();

            $requestStack->getSession()->set('_locale', $user->getLocale());

            $this->addFlash('success', 'user.settings_update_success');

            return $this->redirectToRoute('console_user_account_update');
        }

        return $this->render('console/user/account/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
