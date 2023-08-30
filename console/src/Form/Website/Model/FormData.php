<?php

namespace App\Form\Website\Model;

use App\Entity\Website\Form;
use Symfony\Component\Validator\Constraints as Assert;

class FormData
{
    #[Assert\Length(max: 200)]
    public ?string $title = null;

    #[Assert\Length(max: 200)]
    public ?string $description = null;

    public bool $proposeNewsletter = true;

    public bool $onlyForMembers = false;

    public ?string $redirectUrl = null;

    public array $blocks = [];

    public static function createFromForm(Form $form): self
    {
        $self = new self();
        $self->title = $form->getTitle();
        $self->description = $form->getDescription();
        $self->proposeNewsletter = $form->proposeNewsletter();
        $self->onlyForMembers = $form->isOnlyForMembers();
        $self->redirectUrl = $form->getRedirectUrl();

        foreach ($form->getBlocks() as $block) {
            $self->blocks[] = [
                'id' => $block->getId(),
                'type' => $block->getType(),
                'content' => $block->getContent(),
                'required' => $block->isRequired(),
                'config' => $block->getConfig(),
            ];
        }

        return $self;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'proposeNewsletter' => $this->proposeNewsletter,
            'onlyForMembers' => $this->onlyForMembers,
            'redirectUrl' => $this->redirectUrl,
            'blocks' => $this->blocks,
        ];
    }

    public static function createFromPayload(array $data): self
    {
        $self = new self();
        $self->title = $data['title'];
        $self->description = $data['description'];
        $self->proposeNewsletter = $data['proposeNewsletter'];
        $self->onlyForMembers = $data['onlyForMembers'];
        $self->redirectUrl = $data['redirectUrl'];

        foreach ($data['blocks'] as $block) {
            $self->blocks[] = $block;
        }

        return $self;
    }
}
