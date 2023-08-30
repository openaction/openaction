<?php

namespace App\Controller\Console\User;

use App\Controller\AbstractController;
use App\Entity\Model\NotificationSettings;
use App\Form\User\Model\NotificationSettingsData;
use App\Form\User\NotificationSettingsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/user/notification')]
class NotificationSettingsController extends AbstractController
{
    #[Route('/update', name: 'console_user_notification_settings', methods: ['GET', 'POST'])]
    public function update(EntityManagerInterface $manager, Request $request)
    {
        $user = $this->getUser();
        $data = new NotificationSettingsData($user->getNotificationSettings());

        $form = $this->createForm(NotificationSettingsType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setNotificationSettings(new NotificationSettings($data->events));

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'user.settings_update_success');

            return $this->redirectToRoute('console_user_notification_settings');
        }

        return $this->render('console/user/notifications/settings.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
