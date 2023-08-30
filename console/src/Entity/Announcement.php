<?php

namespace App\Entity;

use App\Repository\AnnouncementRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AnnouncementRepository::class)]
#[ORM\Table(name: 'announcements')]
class Announcement
{
    use Util\EntityIdTrait;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private string $title;

    #[ORM\Column(length: 250)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 250)]
    private string $description;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(max: 50)]
    private ?string $linkText;

    #[ORM\Column(length: 200, nullable: true)]
    #[Assert\Length(max: 200)]
    #[Assert\Url]
    private ?string $linkUrl;

    #[ORM\Column(length: 10)]
    #[Assert\Length(max: 10)]
    private string $locale;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTime $date;

    public function __construct(
        string $title = '',
        string $description = '',
        string $linkText = 'En savoir plus',
        string $linkUrl = null,
        string $locale = 'fr',
        \DateTime $date = null
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->linkText = $linkText;
        $this->linkUrl = $linkUrl;
        $this->locale = $locale;
        $this->date = $date ?: new \DateTime();
    }

    public static function createFixture(array $data): self
    {
        return new self(
            $data['title'],
            $data['description'],
            $data['linkText'] ?? 'Learn more',
            $data['linkUrl'] ?? null,
            $data['locale'] ?? 'en',
            $data['date'] ?? new \DateTime(),
        );
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(?string $title)
    {
        $this->title = $title ?: '';
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(?string $description)
    {
        $this->description = $description;
    }

    public function getLinkText(): ?string
    {
        return $this->linkText ?: '';
    }

    public function setLinkText(?string $linkText)
    {
        $this->linkText = $linkText;
    }

    public function getLinkUrl(): ?string
    {
        return $this->linkUrl ?: '';
    }

    public function setLinkUrl(?string $linkUrl)
    {
        $this->linkUrl = $linkUrl;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale)
    {
        $this->locale = $locale ?: '';
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDate(?\DateTime $date)
    {
        $this->date = $date;
    }
}
