<?php

namespace App\DataManager;

use App\Cdn\CdnUploader;
use App\Entity\Project;
use App\Entity\Website\Event;
use App\Entity\Website\EventCategory;
use App\Repository\Website\EventCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class EventDataManager
{
    private CdnUploader $uploader;
    private EventCategoryRepository $categoryRepository;
    private EntityManagerInterface $em;

    public function __construct(CdnUploader $u, EventCategoryRepository $cr, EntityManagerInterface $em)
    {
        $this->uploader = $u;
        $this->categoryRepository = $cr;
        $this->em = $em;
    }

    public function duplicate(Event $event, bool $keepPublishStatus = false): Event
    {
        $duplicate = $event->duplicate();

        if ($event->getImage()) {
            $duplicate->setImage($this->uploader->duplicate($event->getImage()));
        }

        if ($keepPublishStatus) {
            $duplicate->setPublishedAt($event->getPublishedAt());
        }

        $this->em->persist($duplicate);
        $this->em->flush();

        return $duplicate;
    }

    public function move(Event $event, Project $intoProject): Event
    {
        if ($event->getProject()->getId() === $intoProject->getId()) {
            return $event;
        }

        // Change project
        $event->setProject($intoProject);

        // Create/update necessary categories in the new project
        $intoProjectCategories = [];
        foreach ($this->categoryRepository->getProjectCategories($intoProject) as $category) {
            $intoProjectCategories[$category->getName()] = $category;
        }

        $toAdd = [];
        foreach ($event->getCategories() as $pc) {
            if ($intoProjectCategory = $intoProjectCategories[$pc->getName()] ?? null) {
                $toAdd[] = $intoProjectCategory->getId();
            } else {
                $this->em->persist($c = new EventCategory($intoProject, $pc->getName(), $pc->getWeight()));
                $this->em->flush();

                $toAdd[] = $c->getId();
            }
        }

        $this->em->persist($event);
        $this->em->flush();

        $this->categoryRepository->updateCategories($event, $toAdd);

        return $event;
    }
}
