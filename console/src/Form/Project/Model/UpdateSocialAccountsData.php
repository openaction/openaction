<?php

namespace App\Form\Project\Model;

use App\Entity\Project;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateSocialAccountsData
{
    #[Assert\Email(message: 'console.project.settings.socials.invalid_email')]
    public ?string $email = '';

    #[Assert\Length(max: 50, maxMessage: 'console.project.settings.socials.invalid_length')]
    public ?string $phone = '';

    #[Assert\Length(max: 150, maxMessage: 'console.project.settings.socials.invalid_length')]
    public ?string $facebook = '';

    #[Assert\Length(max: 150, maxMessage: 'console.project.settings.socials.invalid_length')]
    public ?string $instagram = '';

    #[Assert\Length(max: 150, maxMessage: 'console.project.settings.socials.invalid_length')]
    public ?string $twitter = '';

    #[Assert\Length(max: 150, maxMessage: 'console.project.settings.socials.invalid_length')]
    public ?string $linkedIn = '';

    #[Assert\Length(max: 150, maxMessage: 'console.project.settings.socials.invalid_length')]
    public ?string $youtube = '';

    #[Assert\Length(max: 150, maxMessage: 'console.project.settings.socials.invalid_length')]
    public ?string $medium = '';

    #[Assert\Length(max: 50, maxMessage: 'console.project.settings.socials.invalid_length')]
    public ?string $telegram = '';

    #[Assert\Length(max: 50, maxMessage: 'console.project.settings.socials.invalid_length')]
    public ?string $snapchat = '';

    #[Assert\Length(max: 150, maxMessage: 'console.project.settings.socials.invalid_length')]
    #[Assert\Url]
    public ?string $whatsapp = '';

    #[Assert\Length(max: 150, maxMessage: 'console.project.settings.socials.invalid_length')]
    #[Assert\Url]
    public ?string $tiktok = '';

    #[Assert\Length(max: 150, maxMessage: 'console.project.settings.socials.invalid_length')]
    #[Assert\Url]
    public ?string $threads = '';

    public function __construct(
        ?string $email = '',
        ?string $phone = '',
        ?string $facebook = '',
        ?string $instagram = '',
        ?string $twitter = '',
        ?string $linkedIn = '',
        ?string $youtube = '',
        ?string $medium = '',
        ?string $telegram = '',
        ?string $snapchat = '',
        ?string $whatsapp = '',
        ?string $tiktok = '',
        ?string $threads = '',
    ) {
        $this->email = $email;
        $this->phone = $phone;
        $this->facebook = $facebook;
        $this->instagram = $instagram;
        $this->twitter = $twitter;
        $this->linkedIn = $linkedIn;
        $this->youtube = $youtube;
        $this->medium = $medium;
        $this->telegram = $telegram;
        $this->snapchat = $snapchat;
        $this->whatsapp = $whatsapp;
        $this->tiktok = $tiktok;
        $this->threads = $threads;
    }

    public static function createFromProject(Project $project): self
    {
        $data = new self();
        $data->email = $project->getSocialEmail();
        $data->phone = $project->getSocialPhone();
        $data->facebook = $project->getSocialFacebook();
        $data->instagram = $project->getSocialInstagram();
        $data->twitter = $project->getSocialTwitter();
        $data->linkedIn = $project->getSocialLinkedIn();
        $data->youtube = $project->getSocialYoutube();
        $data->medium = $project->getSocialMedium();
        $data->telegram = $project->getSocialTelegram();
        $data->snapchat = $project->getSocialSnapchat();
        $data->whatsapp = $project->getSocialWhatsapp();
        $data->tiktok = $project->getSocialTiktok();
        $data->threads = $project->getSocialThreads();

        return $data;
    }
}
