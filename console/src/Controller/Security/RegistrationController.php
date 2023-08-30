<?php

namespace App\Controller\Security;

use App\Entity\Organization;
use App\Entity\OrganizationMember;
use App\Entity\Registration;
use App\Form\User\Model\RegistrationFinalizingData;
use App\Form\User\Model\RegistrationRequestData;
use App\Form\User\RegistrationFinalizingType;
use App\Form\User\RegistrationType;
use App\Mailer\PlatformMailer;
use App\Repository\UserRepository;
use App\Search\TenantTokenManager;
use App\Security\Registration\ManualAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/register')]
class RegistrationController extends AbstractController
{
    private EntityManagerInterface $em;
    private UserRepository $repository;
    private PlatformMailer $mailer;

    public function __construct(EntityManagerInterface $em, UserRepository $repository, PlatformMailer $mailer)
    {
        $this->em = $em;
        $this->repository = $repository;
        $this->mailer = $mailer;
    }

    #[Route('', name: 'security_register', methods: ['GET', 'POST'])]
    public function signup(Request $request)
    {
        $form = $this->createForm(RegistrationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var RegistrationRequestData $data */
            $data = $form->getData();

            if ($this->repository->findOneBy(['email' => $data->getEmail()]) instanceof UserInterface) {
                $this->addFlash('error', 'registration.error_duplicated');

                return $this->redirectToRoute('security_register');
            }

            $registration = new Registration($data->getEmail());
            $this->em->persist($registration);
            $this->em->flush();

            $this->mailer->sendRegistrationVerify($registration);

            return $this->redirectToRoute('security_register_verify', ['uuid' => $registration->getUuid()]);
        }

        return $this->render('security/registration/signup.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{uuid}/verify', name: 'security_register_verify', methods: ['GET'])]
    public function verify(Registration $registration)
    {
        return $this->render('security/registration/verify.html.twig', [
            'registration' => $registration,
        ]);
    }

    #[Route('/{uuid}/resend-email', name: 'security_registration_resend', methods: ['GET'])]
    public function resend(Registration $registration)
    {
        $this->mailer->sendRegistrationVerify($registration);

        return $this->redirectToRoute('security_register_verify', ['uuid' => $registration->getUuid()]);
    }

    #[Route('/{uuid}/activate/{token}', name: 'security_register_finalizing', methods: ['GET', 'POST'])]
    public function activate(ManualAuthenticator $authenticator, TenantTokenManager $tenantTokenManager, Registration $registration, Request $request, string $token)
    {
        if (!$registration->isTokenValid($token)) {
            throw $this->createNotFoundException();
        }

        $data = new RegistrationFinalizingData();

        $form = $this->createForm(RegistrationFinalizingType::class, $data);
        $form->handleRequest($request);

        // Check if the user already exists: if not, create it
        $account = $this->repository->findOneBy(['email' => $registration->getEmail()]);

        if (!$account && $form->isSubmitted() && $form->isValid()) {
            $account = $this->repository->createUserAccount(
                $registration->getEmail(),
                $data->firstName,
                $data->lastName,
                $data->password,
                $registration->getLocale()
            );

            $this->mailer->sendRegistrationWelcome($account);
        }

        // If the registration occured due to an invite, add the account to the orga
        if ($account instanceof UserInterface) {
            if ($registration->getOrganization() instanceof Organization) {
                $member = OrganizationMember::createFromRegistration($account, $registration);
                $tenantTokenManager->refreshMemberCrmTenantToken($member, persist: true);
            }

            // Remove the registration once finalized
            $this->em->remove($registration);
            $this->em->flush();

            $authenticator->authenticate($account);

            return $this->redirectToRoute('homepage_redirect', ['force_orga' => '1']);
        }

        return $this->render('security/registration/activate.html.twig', [
            'form' => $form->createView(),
            'registration' => $registration,
        ]);
    }
}
