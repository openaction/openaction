<?php

namespace App\Twig;

use App\Repository\AnnouncementRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AnnouncementExtension extends AbstractExtension
{
    private AnnouncementRepository $repository;

    public function __construct(AnnouncementRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_announcements_for', [$this->repository, 'findAnnouncementsFor']),
        ];
    }
}
