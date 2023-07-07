<?php

namespace App\Entity\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ProjectTerminology
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 40)]
    private string $posts;

    #[Assert\NotBlank]
    #[Assert\Length(max: 40)]
    private string $events;

    #[Assert\NotBlank]
    #[Assert\Length(max: 40)]
    private string $trombinoscope;

    #[Assert\NotBlank]
    #[Assert\Length(max: 40)]
    private string $manifesto;

    #[Assert\NotBlank]
    #[Assert\Length(max: 40)]
    private string $newsletter;

    #[Assert\NotBlank]
    #[Assert\Length(max: 200)]
    private string $acceptPrivacy;

    #[Assert\NotBlank]
    #[Assert\Length(max: 40)]
    private string $socialNetworks;

    #[Assert\NotBlank]
    #[Assert\Length(max: 40)]
    private string $membershipLogin;

    #[Assert\NotBlank]
    #[Assert\Length(max: 40)]
    private string $membershipRegister;

    #[Assert\NotBlank]
    #[Assert\Length(max: 40)]
    private string $membershipArea;

    public function __construct(array $data)
    {
        $this->posts = $data['posts'] ?? 'Actualités';
        $this->events = $data['events'] ?? 'Événements';
        $this->trombinoscope = $data['trombinoscope'] ?? 'Notre équipe';
        $this->manifesto = $data['manifesto'] ?? 'Nos propositions';
        $this->newsletter = $data['newsletter'] ?? 'Recevoir la newsletter';
        $this->acceptPrivacy = $data['acceptPrivacy'] ?? 'Je consens au traitement de mes données et accepte la Politique de protection des données';
        $this->socialNetworks = $data['socialNetworks'] ?? 'Réseaux sociaux';
        $this->membershipLogin = $data['membershipLogin'] ?? 'Se connecter';
        $this->membershipRegister = $data['membershipRegister'] ?? 'S\'inscrire';
        $this->membershipArea = $data['membershipArea'] ?? 'Mon espace membre';
    }

    public function toArray(): array
    {
        return [
            'posts' => $this->posts,
            'events' => $this->events,
            'trombinoscope' => $this->trombinoscope,
            'manifesto' => $this->manifesto,
            'newsletter' => $this->newsletter,
            'acceptPrivacy' => $this->acceptPrivacy,
            'socialNetworks' => $this->socialNetworks,
            'membershipLogin' => $this->membershipLogin,
            'membershipRegister' => $this->membershipRegister,
            'membershipArea' => $this->membershipArea,
        ];
    }

    public function getPosts(): string
    {
        return $this->posts;
    }

    public function setPosts(string $posts)
    {
        $this->posts = $posts;
    }

    public function getEvents(): string
    {
        return $this->events;
    }

    public function setEvents(string $events)
    {
        $this->events = $events;
    }

    public function getTrombinoscope(): string
    {
        return $this->trombinoscope;
    }

    public function setTrombinoscope(string $trombinoscope)
    {
        $this->trombinoscope = $trombinoscope;
    }

    public function getManifesto(): string
    {
        return $this->manifesto;
    }

    public function setManifesto(string $manifesto)
    {
        $this->manifesto = $manifesto;
    }

    public function getNewsletter(): string
    {
        return $this->newsletter;
    }

    public function setNewsletter(string $newsletter)
    {
        $this->newsletter = $newsletter;
    }

    public function getAcceptPrivacy(): string
    {
        return $this->acceptPrivacy;
    }

    public function setAcceptPrivacy(string $acceptPrivacy)
    {
        $this->acceptPrivacy = $acceptPrivacy;
    }

    public function getSocialNetworks(): string
    {
        return $this->socialNetworks;
    }

    public function setSocialNetworks(string $socialNetworks)
    {
        $this->socialNetworks = $socialNetworks;
    }

    public function getMembershipLogin(): string
    {
        return $this->membershipLogin;
    }

    public function setMembershipLogin(string $membershipLogin)
    {
        $this->membershipLogin = $membershipLogin;
    }

    public function getMembershipRegister(): string
    {
        return $this->membershipRegister;
    }

    public function setMembershipRegister(string $membershipRegister)
    {
        $this->membershipRegister = $membershipRegister;
    }

    public function getMembershipArea(): string
    {
        return $this->membershipArea;
    }

    public function setMembershipArea(string $membershipArea)
    {
        $this->membershipArea = $membershipArea;
    }
}
