<?php

namespace App\Entity;

use App\Entity\Model\CloudflareDomainConfig;
use App\Entity\Model\PostmarkDomainConfig;
use App\Entity\Model\SendgridDomainConfig;
use App\Repository\DomainRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DomainRepository::class)]
#[ORM\Table('domains')]
class Domain implements \Stringable
{
    use Util\EntityIdTrait;

    #[ORM\ManyToOne(targetEntity: Organization::class, inversedBy: 'domains')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Organization $organization;

    #[ORM\Column(length: 150, unique: true)]
    private string $name;

    #[ORM\Column(type: 'json')]
    private array $configurationStatus = ['cloudflare_pending' => 1];

    #[ORM\Column(type: 'json')]
    private array $cloudflareConfig = [];

    #[ORM\Column(type: 'json')]
    private array $sendgridConfig = [];

    #[ORM\Column(type: 'json')]
    private array $postmarkConfig = [];

    #[ORM\Column(type: 'boolean')]
    private bool $managedAutomatically = true;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $lastCheckedAt;

    /**
     * @var Collection|Project[]
     */
    #[ORM\OneToMany(targetEntity: Project::class, mappedBy: 'rootDomain', orphanRemoval: false)]
    #[ORM\OrderBy(['name' => 'ASC'])]
    private ?Collection $projects;

    public function __construct(Organization $organization, string $name)
    {
        $this->organization = $organization;
        $this->name = $name;
        $this->projects = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function setOrganization(?Organization $organization)
    {
        $this->organization = $organization;
    }

    public function setManagedAutomatically(bool $managedAutomatically)
    {
        $this->managedAutomatically = $managedAutomatically;
    }

    public function markChecked()
    {
        $this->lastCheckedAt = new \DateTime();
    }

    public function getConfigurationStatus(): array
    {
        return $this->configurationStatus;
    }

    public function setConfigurationStatus(array $configurationStatus)
    {
        $this->configurationStatus = $configurationStatus;
    }

    public function setCloudflareConfig(CloudflareDomainConfig $config)
    {
        $this->cloudflareConfig = $config->toArray();
        $this->markChecked();
    }

    public function getCloudflareConfig(): CloudflareDomainConfig
    {
        return CloudflareDomainConfig::fromConfig($this->cloudflareConfig);
    }

    public function getSendgridConfig(): SendgridDomainConfig
    {
        return SendgridDomainConfig::fromConfig($this->sendgridConfig);
    }

    public function setSendgridConfig(SendgridDomainConfig $sendgridConfig)
    {
        $this->sendgridConfig = $sendgridConfig->toArray();
    }

    public function getPostmarkConfig(): PostmarkDomainConfig
    {
        return PostmarkDomainConfig::fromConfig($this->postmarkConfig);
    }

    public function setPostmarkConfig(PostmarkDomainConfig $postmarkConfig)
    {
        $this->postmarkConfig = $postmarkConfig->toArray();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function getLastCheckedAt(): ?\DateTime
    {
        return $this->lastCheckedAt;
    }

    public function isManagedAutomatically(): bool
    {
        return $this->managedAutomatically;
    }

    /**
     * @return Project[]|Collection
     */
    public function getProjects()
    {
        return $this->projects;
    }
}
