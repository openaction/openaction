<?php

namespace App\DataManager;

use App\Cdn\CdnUploader;
use App\Entity\Project;
use App\Entity\Website\Post;
use App\Entity\Website\PostCategory;
use App\Repository\Website\PostCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class PostDataManager
{
    private CdnUploader $uploader;
    private PostCategoryRepository $categoryRepository;
    private EntityManagerInterface $em;

    public function __construct(CdnUploader $u, PostCategoryRepository $cr, EntityManagerInterface $em)
    {
        $this->uploader = $u;
        $this->categoryRepository = $cr;
        $this->em = $em;
    }

    public function duplicate(Post $post, bool $keepPublishStatus = false): Post
    {
        $duplicate = $post->duplicate();

        if ($post->getImage()) {
            $duplicate->setImage($this->uploader->duplicate($post->getImage()));
        }

        if ($keepPublishStatus) {
            $duplicate->setPublishedAt($post->getPublishedAt());
        }

        $this->em->persist($duplicate);
        $this->em->flush();

        return $duplicate;
    }

    public function move(Post $post, Project $intoProject): Post
    {
        if ($post->getProject()->getId() === $intoProject->getId()) {
            return $post;
        }

        // Change project
        $post->setProject($intoProject);

        // Create/update necessary categories in the new project
        $intoProjectCategories = [];
        foreach ($this->categoryRepository->getProjectCategories($intoProject) as $category) {
            $intoProjectCategories[$category->getName()] = $category;
        }

        $toAdd = [];
        foreach ($post->getCategories() as $pc) {
            if ($intoProjectCategory = $intoProjectCategories[$pc->getName()] ?? null) {
                $toAdd[] = $intoProjectCategory->getId();
            } else {
                $this->em->persist($c = new PostCategory($intoProject, $pc->getName(), $pc->getWeight()));
                $this->em->flush();

                $toAdd[] = $c->getId();
            }
        }

        $this->em->persist($post);
        $this->em->flush();

        $this->categoryRepository->updateCategories($post, $toAdd);

        return $post;
    }
}
