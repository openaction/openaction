<?php

namespace App\DataManager;

use App\Cdn\CdnUploader;
use App\Entity\Project;
use App\Entity\Website\TrombinoscopeCategory;
use App\Entity\Website\TrombinoscopePerson;
use App\Repository\Website\TrombinoscopeCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class TrombinoscopeDataManager
{
    private CdnUploader $uploader;
    private TrombinoscopeCategoryRepository $categoryRepository;
    private EntityManagerInterface $em;

    public function __construct(CdnUploader $u, TrombinoscopeCategoryRepository $cr, EntityManagerInterface $em)
    {
        $this->uploader = $u;
        $this->categoryRepository = $cr;
        $this->em = $em;
    }

    public function duplicate(TrombinoscopePerson $person, bool $keepPublishStatus = false): TrombinoscopePerson
    {
        $duplicate = $person->duplicate();

        if ($person->getImage()) {
            $duplicate->setImage($this->uploader->duplicate($person->getImage()));
        }

        if ($keepPublishStatus) {
            $duplicate->setPublishedAt($person->getPublishedAt());
        }

        $this->em->persist($duplicate);
        $this->em->flush();

        return $duplicate;
    }

    public function move(TrombinoscopePerson $person, Project $intoProject): TrombinoscopePerson
    {
        if ($person->getProject()?->getId() === $intoProject->getId()) {
            return $person;
        }

        // Change project
        $person->setProject($intoProject);

        // Create/update necessary categories in the new project
        $intoProjectCategories = [];
        foreach ($this->categoryRepository->getProjectCategories($intoProject) as $category) {
            $intoProjectCategories[$category->getName()] = $category;
        }

        $toAdd = [];
        foreach ($person->getCategories() as $pc) {
            if ($intoProjectCategory = $intoProjectCategories[$pc->getName()] ?? null) {
                $toAdd[] = $intoProjectCategory->getId();
            } else {
                $this->em->persist($c = new TrombinoscopeCategory($intoProject, $pc->getName(), $pc->getWeight()));
                $this->em->flush();

                $toAdd[] = $c->getId();
            }
        }

        $this->em->persist($person);
        $this->em->flush();

        $this->categoryRepository->updateCategories($person, $toAdd);

        return $person;
    }
}
