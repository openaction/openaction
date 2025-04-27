<?php

namespace App\Controller;

use App\Billing\Expiration\ExpiredSubscriptionException;
use App\Entity\Organization;
use App\Entity\Project;
use App\Entity\User;
use App\Security\Csrf\GlobalCsrfTokenManager;
use App\Security\TwoFactor\TwoFactorAuthRequiredException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;

abstract class AbstractController extends BaseController
{
    public function denyUnlessValidCsrf(Request $request): void
    {
        if (!$token = $request->headers->get('X-XSRF-TOKEN', $request->query->get('_token'))) {
            throw $this->createAccessDeniedException('CSRF token not found.');
        }

        if (!$this->container->get(GlobalCsrfTokenManager::class)->isTokenValid($token)) {
            throw $this->createAccessDeniedException('Invalid CSRF token provided.');
        }
    }

    public function denyUnlessSameOrganization($entity): void
    {
        if (!$entity) {
            throw $this->createAccessDeniedException('Entity not found, current organization check couldn\'t be done.');
        }

        if (!method_exists($entity, 'getOrganization')) {
            throw new \LogicException(sprintf('Entity %s of type %s has no getOrganization() method.', $entity->getId(), get_class($entity)));
        }

        if ($entity->getOrganization()->getId() !== $this->getOrganization()->getId()) {
            throw $this->createAccessDeniedException('Invalid organization for current entity.');
        }
    }

    public function denyUnlessSameProject($entity): void
    {
        if (!$entity) {
            throw $this->createNotFoundException('Entity not found, current project check couldn\'t be done.');
        }

        if (!method_exists($entity, 'getProject')) {
            throw new \LogicException(sprintf('Entity %s of type %s has no getProject() method.', $entity->getId(), get_class($entity)));
        }

        if ($entity->getProject()->getId() !== $this->getProject()->getId()) {
            throw $this->createAccessDeniedException('Invalid project for current entity.');
        }
    }

    public function denyUnlessShowPreview(): void
    {
        if (!($orga = $this->getOrganization()) || !$orga->isShowPreview()) {
            throw $this->createAccessDeniedException('This organization is not allowed to preview features.');
        }
    }

    public function denyUnlessFeatureInPlan(string $feature): void
    {
        if (!($orga = $this->getOrganization()) || !$orga->isFeatureInPlan($feature)) {
            throw $this->createAccessDeniedException('This organization is not allowed to access this feature.');
        }
    }

    public function denyIfSubscriptionExpired(): void
    {
        if (!$orga = $this->getOrganization()) {
            return;
        }

        if (!$orga->isSubscriptionActive()) {
            throw new ExpiredSubscriptionException($orga);
        }
    }

    public function requireTwoFactorAuthIfForced(): void
    {
        if (!$orga = $this->getOrganization()) {
            return;
        }

        if ($orga->hasForceTwoFactorAuth() && !$this->getUser()?->isTwoFactorEnabled()) {
            throw new TwoFactorAuthRequiredException($orga);
        }
    }

    public function getRawUser()
    {
        return parent::getUser();
    }

    public function getUser(): ?User
    {
        return parent::getUser();
    }

    public function getOrganization(): ?Organization
    {
        return $this->container->get('request_stack')->getCurrentRequest()->attributes->get('organization');
    }

    public function getProject(): ?Project
    {
        return $this->container->get('request_stack')->getCurrentRequest()->attributes->get('project');
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            GlobalCsrfTokenManager::class => GlobalCsrfTokenManager::class,
        ]);
    }

    /**
     * Helper to extract form errors into a structured array.
     * Note: This is a simplified version. Getting exact field names reliably
     * for nested forms and complex errors can be tricky.
     */
    protected function getFormErrors(FormInterface $form): array
    {
        $errors = [];
        foreach ($form->getErrors(true, true) as $error) {
            $errors[] = $error->getMessage();
        }

        // Also collect errors from children explicitly
        foreach ($form->all() as $child) {
            if ($child instanceof FormInterface) {
                $childErrors = $this->getFormErrors($child);
                if (!empty($childErrors)) {
                    // Prepend child name to make it somewhat identifiable
                    foreach($childErrors as $childError) {
                        $errors[] = $child->getName() . ': ' . $childError;
                    }
                }
            }
        }

        return $errors;
    }
}
