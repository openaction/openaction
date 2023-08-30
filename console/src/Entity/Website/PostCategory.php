<?php

namespace App\Entity\Website;

use App\Entity\Project;
use App\Entity\Util;
use App\Repository\Website\PostCategoryRepository;
use App\Util\Uid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PostCategoryRepository::class)]
#[ORM\Table(name: 'website_posts_categories')]
class PostCategory
{
    use Util\EntityUuidTrait;
    use Util\EntityIdTrait;
    use Util\EntityProjectTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\Column(type: 'string', length: 40)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 40)]
    private string $name;

    #[ORM\Column(type: 'string', length: 50)]
    private string $slug;

    #[ORM\Column(type: 'integer')]
    private int $weight;

    /**
     * @var Collection|Post[]
     */
    #[ORM\ManyToMany(targetEntity: Post::class, mappedBy: 'categories')]
    #[ORM\JoinTable(name: 'website_posts_posts_categories')]
    private Collection $posts;

    public function __construct(Project $project, string $name, int $weight = 1)
    {
        $this->populateTimestampable();
        $this->uuid = Uid::random();
        $this->project = $project;
        $this->setName($name);
        $this->weight = $weight;
        $this->posts = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    public static function createFixture(array $data): self
    {
        $self = new self($data['project'], $data['name'], $data['weight'] ?? 1);

        if (isset($data['uuid']) && $data['uuid']) {
            $self->uuid = Uuid::fromString($data['uuid']);
        }

        return $self;
    }

    public function setName(?string $name)
    {
        $this->name = (string) $name;
        $this->slug = (new AsciiSlugger())->slug($this->name)->lower();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }
}
