<?php

namespace App\Form\Project\Model;

use App\Entity\Project;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateSocialAccountsData
{
    #[Assert\Email(message: 'console.project.settings.socials.invalid_email')]
    public ?string $email = '';

    #[Assert\Length(max: 150, maxMessage: 'console.project.settings.socials.invalid_length')]
    #[Assert\Regex(pattern: '/^(http(s?):\/\/)?(www\.)?facebook\.com\/(_|-|[a-z]|[0-9]|\.)+\/?$/i', message: 'console.project.settings.socials.invalid_facebook')]
    public ?string $facebook = '';

    #[Assert\Length(max: 150, maxMessage: 'console.project.settings.socials.invalid_length')]
    #[Assert\Regex(pattern: '/^(http(s?):\/\/)?(www\.)?instagram\.com\/(_|-|[a-z]|[0-9]|\.)+\/?$/i', message: 'console.project.settings.socials.invalid_instagram')]
    public ?string $instagram = '';

    #[Assert\Length(max: 150, maxMessage: 'console.project.settings.socials.invalid_length')]
    #[Assert\Regex(pattern: '/^(http(s?):\/\/)?(www\.)?twitter\.com\/(_|-|[a-z]|[0-9]|\.)+\/?$/i', message: 'console.project.settings.socials.invalid_twitter')]
    public ?string $twitter = '';

    #[Assert\Length(max: 150, maxMessage: 'console.project.settings.socials.invalid_length')]
    #[Assert\Regex(pattern: '/^(http(s?):\/\/)?(www\.|[a-z]{1,3}\.)?linkedin\.com\/(_|-|[a-z]|[0-9]|\.|\/)+\/?$/i', message: 'console.project.settings.socials.invalid_linkedin')]
    public ?string $linkedIn = '';

    #[Assert\Length(max: 150, maxMessage: 'console.project.settings.socials.invalid_length')]
    #[Assert\Regex(pattern: '/^(http(s?):\/\/)?(www\.)?youtube\.com\/(_|-|[a-z]|[0-9]|\.|\/)+\/?$/i', message: 'console.project.settings.socials.invalid_youtube')]
    public ?string $youtube = '';

    #[Assert\Length(max: 150, maxMessage: 'console.project.settings.socials.invalid_length')]
    #[Assert\Regex(pattern: '/^(http(s?):\/\/)?(www\.|[a-z]{1,16}\.)?medium\.com\/(_|-|[a-z]|[0-9]|\.|\/)+\/?$/i', message: 'console.project.settings.socials.invalid_medium')]
    public ?string $medium = '';

    #[Assert\Length(max: 50, maxMessage: 'console.project.settings.socials.invalid_length')]
    #[Assert\Regex(pattern: '/^([a-z]|[0-9]|_){2,50}$/i', message: 'console.project.settings.socials.invalid_telegram')]
    public ?string $telegram = '';

    #[Assert\Length(max: 50, maxMessage: 'console.project.settings.socials.invalid_length')]
    #[Assert\Regex(pattern: '/^([a-z]|[0-9]|_){2,50}$/i', message: 'console.project.settings.socials.invalid_snapchat')]
    public ?string $snapchat = '';

    public static function createFromProject(Project $project): self
    {
        $data = new self();
        $data->email = $project->getSocialEmail();
        $data->facebook = $project->getSocialFacebook();
        $data->instagram = $project->getSocialInstagram();
        $data->twitter = $project->getSocialTwitter();
        $data->linkedIn = $project->getSocialLinkedIn();
        $data->youtube = $project->getSocialYoutube();
        $data->medium = $project->getSocialMedium();
        $data->telegram = $project->getSocialTelegram();
        $data->snapchat = $project->getSocialSnapchat();

        return $data;
    }
}
