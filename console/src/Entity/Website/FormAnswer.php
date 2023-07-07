<?php

namespace App\Entity\Website;

use App\Entity\Community\Contact;
use App\Entity\Util;
use App\Repository\Website\FormAnswerRepository;
use App\Util\Uid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: FormAnswerRepository::class)]
#[ORM\Table('website_forms_answers')]
class FormAnswer
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\ManyToOne(targetEntity: Form::class, inversedBy: 'answers')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Form $form;

    #[ORM\ManyToOne(targetEntity: Contact::class, inversedBy: 'formAnswers')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Contact $contact;

    #[ORM\Column(type: 'json')]
    private array $answers;

    public function __construct(Form $form, ?Contact $contact, array $answers = [])
    {
        $this->populateTimestampable();
        $this->uuid = Uid::random();
        $this->form = $form;
        $this->contact = $contact;
        $this->answers = $answers;
    }

    public static function createFixture(array $data): self
    {
        $self = new self($data['form'], $data['contact'], $data['answers'] ?? []);

        if (isset($data['uuid']) && $data['uuid']) {
            $self->uuid = Uuid::fromString($data['uuid']);
        }

        return $self;
    }

    public function getForm(): Form
    {
        return $this->form;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function getAnswers(): array
    {
        return $this->answers;
    }
}
