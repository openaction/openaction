<?php

namespace App\Form\Project;

use App\Entity\Community\Model\ContentImportSettings;
use RuntimeException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContentImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['import_source'] === ContentImportSettings::IMPORT_SOURCE_WORDPRESS) {
            // add all options related to WordPress import
            $builder->add('postSaveStatus', ChoiceType::class, [
                'choices' => [
                    'Import all posts as drafts' => ContentImportSettings::POST_STATUS_SAVE_AS_DRAFT,
                    'Import all posts with their original status' => ContentImportSettings::POST_STATUS_SAVE_AS_ORIGINAL,
                ],
                'multiple' => false,
                'expanded' => true,
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ]);
        } else {
            throw new RuntimeException('Missing import source in content type!');
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'import_source' => null,
        ]);
    }
}