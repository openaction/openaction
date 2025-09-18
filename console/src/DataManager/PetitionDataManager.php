<?php

namespace App\DataManager;

use App\Entity\Project;
use App\Entity\Website\Form;
use App\Entity\Website\FormBlock;
use App\Entity\Website\LocalizedPetition;
use App\Entity\Website\Petition;
use App\Repository\Website\PetitionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class PetitionDataManager
{
    public function __construct(
        private readonly PetitionRepository $repository,
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function createEmptyPetition(Project $project, string $firstLocale): LocalizedPetition
    {
        // Create empty petition with default slug and a first localization with default title
        $count = $this->repository->count(['project' => $project]);

        $petition = new Petition($project, 'untitled-petition-'.($count + 1));
        $this->em->persist($petition);
        $this->em->flush();

        return $this->createEmptyLocalizedPetition($petition, $firstLocale);
    }

    public function createEmptyLocalizedPetition(Petition $petition, string $locale): LocalizedPetition
    {
        $defaultTitle = $this->translator->trans('create.default_title', [], 'project_petitions');

        // Create form
        $form = new Form($petition->getProject(), $defaultTitle);
        $this->em->persist($form);

        $blocks = [
            ['type' => FormBlock::TYPE_FIRST_NAME, 'content' => 'create.formBlocks.first_name'],
            ['type' => FormBlock::TYPE_LAST_NAME, 'content' => 'create.formBlocks.last_name'],
            ['type' => FormBlock::TYPE_EMAIL, 'content' => 'create.formBlocks.email'],
            ['type' => FormBlock::TYPE_PHONE, 'content' => 'create.formBlocks.phone'],
            ['type' => FormBlock::TYPE_ZIP_CODE, 'content' => 'create.formBlocks.zip_code'],
            ['type' => FormBlock::TYPE_CITY, 'content' => 'create.formBlocks.city'],
            ['type' => FormBlock::TYPE_COUNTRY, 'content' => 'create.formBlocks.country'],
        ];

        foreach ($blocks as $block) {
            $content = $this->translator->trans($block['content'], [], 'project_petitions');
            $this->em->persist(new FormBlock($form, $block['type'], $content, true));
        }

        $this->em->flush();

        // Create localized petition
        $localized = new LocalizedPetition(
            petition: $petition,
            form: $form,
            locale: $locale,
            title: $defaultTitle,
            submitButtonLabel: $this->translator->trans('create.submitLabel', [], 'project_petitions'),
            optinLabel: $this->translator->trans('create.optinLabel', [], 'project_petitions'),
            legalities: $this->translator->trans('create.legalities', [], 'project_petitions'),
        );

        $this->em->persist($localized);
        $this->em->flush();

        return $localized;
    }
}
