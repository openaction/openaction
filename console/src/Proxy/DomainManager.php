<?php

namespace App\Proxy;

use App\Entity\Domain;
use App\Entity\Organization;
use App\Entity\Project;
use App\Proxy\Consumer\CloudflareCreateDomainMessage;
use App\Proxy\Consumer\ConfigureTrialSubdomainMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class DomainManager
{
    private EntityManagerInterface $em;
    private MessageBusInterface $bus;
    private SluggerInterface $slugger;

    public function __construct(EntityManagerInterface $em, MessageBusInterface $bus, SluggerInterface $slugger)
    {
        $this->em = $em;
        $this->bus = $bus;
        $this->slugger = $slugger;
    }

    public function getTrialDomain(): Domain
    {
        return $this->em->getRepository(Domain::class)->getTrialDomain();
    }

    public function getDomain(string $name): ?Domain
    {
        return $this->em->getRepository(Domain::class)->findOneBy(['name' => $name]);
    }

    public function generateTrialSubdomain(Project $project): string
    {
        return implode('-', [
            $this->slugger->slug($project->getName())->lower(),
            substr($project->getUuid(), 0, 4),
        ]);
    }

    public function connectTrialSubdomain(string $subdomain)
    {
        $this->bus->dispatch(new ConfigureTrialSubdomainMessage($subdomain));
    }

    public function createDomain(Organization $organization, string $name): Domain
    {
        if ($domain = $this->getDomain($name)) {
            return $domain;
        }

        $this->em->persist($domain = new Domain($organization, $name));
        $this->em->flush();

        $this->bus->dispatch(new CloudflareCreateDomainMessage($domain->getId()));

        return $domain;
    }
}
