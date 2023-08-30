<?php

namespace App\DataManager;

use App\Entity\Project;
use App\Entity\Website\Form;
use Doctrine\ORM\EntityManagerInterface;

class FormDataManager
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function duplicate(Form $form): Form
    {
        $duplicate = $form->duplicate();

        $this->em->persist($duplicate);
        $this->em->flush();

        return $duplicate;
    }

    public function move(Form $form, Project $intoProject): Form
    {
        if ($form->getProject()->getId() === $intoProject->getId()) {
            return $form;
        }

        // Can't move a form with answers
        if ($form->getAnswers()->count() > 0) {
            return $form;
        }

        $form->setProject($intoProject);

        $this->em->persist($form);
        $this->em->flush();

        return $form;
    }
}
