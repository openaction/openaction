<?php

namespace App\Controller;

use App\Billing\Expiration\ExpiredSubscriptionException;
use App\Entity\Organization;
use App\Entity\Project;
use App\Entity\User;
use App\Security\Csrf\GlobalCsrfTokenManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseController;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractController extends BaseController
{
    public function denyUnlessValidCsrf(Request $request)
    {
        if (!$token = $request->headers->get('X-XSRF-TOKEN', $request->query->get('_token'))) {
            throw $this->createAccessDeniedException('CSRF token not found.');
        }

        if (!$this->container->get(GlobalCsrfTokenManager::class)->isTokenValid($token)) {
            throw $this->createAccessDeniedException('Invalid CSRF token provided.');
        }
    }

    public function denyUnlessSameOrganization($entity)
    {
        if (!$entity) {
            throw $this->createNotFoundException('Entity not found, current organization check couldn\'t be done.');
        }

        if (!method_exists($entity, 'getOrganization')) {
            throw new \LogicException(sprintf('Entity %s of type %s has no getOrganization() method.', $entity->getId(), get_class($entity)));
        }

        if ($entity->getOrganization()->getId() !== $this->getOrganization()->getId()) {
            throw $this->createAccessDeniedException('Invalid organization for current entity.');
        }
    }

    public function denyUnlessSameProject($entity)
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

    public function denyUnlessShowPreview()
    {
        if (!($orga = $this->getOrganization()) || !$orga->isShowPreview()) {
            throw $this->createNotFoundException('This organization is not allowed to preview features.');
        }
    }

    public function denyUnlessFeatureInPlan(string $feature)
    {
        if (!($orga = $this->getOrganization()) || !$orga->isFeatureInPlan($feature)) {
            throw $this->createNotFoundException('This organization is not allowed to access this feature.');
        }
    }

    public function denyIfSubscriptionExpired()
    {
        if (!$orga = $this->getOrganization()) {
            return;
        }

        if (!$orga->isSubscriptionActive()) {
            throw new ExpiredSubscriptionException($orga);
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
}
