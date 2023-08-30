<?php

namespace App\DataManager;

use App\Cdn\CdnUploader;
use App\Entity\Project;
use App\Entity\Website\Page;
use App\Entity\Website\PageCategory;
use App\Repository\Website\PageCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class PageDataManager
{
    private CdnUploader $uploader;
    private PageCategoryRepository $categoryRepository;
    private EntityManagerInterface $em;

    public function __construct(CdnUploader $u, PageCategoryRepository $cr, EntityManagerInterface $em)
    {
        $this->uploader = $u;
        $this->categoryRepository = $cr;
        $this->em = $em;
    }

    public function duplicate(Page $page): Page
    {
        $duplicate = $page->duplicate();
        if ($page->getImage()) {
            $duplicate->setImage($this->uploader->duplicate($page->getImage()));
        }

        $this->em->persist($duplicate);
        $this->em->flush();

        return $duplicate;
    }

    public function move(Page $page, Project $intoProject): Page
    {
        if ($page->getProject()->getId() === $intoProject->getId()) {
            return $page;
        }

        // Change project
        $page->setProject($intoProject);

        // Create/update necessary categories in the new project
        $intoProjectCategories = [];
        foreach ($this->categoryRepository->getProjectCategories($intoProject) as $category) {
            $intoProjectCategories[$category->getName()] = $category;
        }

        $toAdd = [];
        foreach ($page->getCategories() as $pc) {
            if ($intoProjectCategory = $intoProjectCategories[$pc->getName()] ?? null) {
                $toAdd[] = $intoProjectCategory->getId();
            } else {
                $this->em->persist($c = new PageCategory($intoProject, $pc->getName(), $pc->getWeight()));
                $this->em->flush();

                $toAdd[] = $c->getId();
            }
        }

        $this->em->persist($page);
        $this->em->flush();

        $this->categoryRepository->updateCategories($page, $toAdd);

        return $page;
    }
}
