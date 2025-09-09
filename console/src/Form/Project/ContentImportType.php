<?php

namespace App\Form\Project;

use App\Entity\Community\Model\ContentImportSettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContentImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (ContentImportSettings::IMPORT_SOURCE_WORDPRESS === $options['import_source']) {
            $builder->add('postSaveStatus', ChoiceType::class, [
                'translation_domain' => 'project_configuration',
                'choices' => [
                    'content_import.wordpress.settings.options.post_save_status.as_draft' => ContentImportSettings::POST_STATUS_SAVE_AS_DRAFT,
                    'content_import.wordpress.settings.options.post_save_status.as_original' => ContentImportSettings::POST_STATUS_SAVE_AS_ORIGINAL,
                ],
                'multiple' => false,
                'expanded' => true,
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ]);

            $builder->add('keepCategories', ChoiceType::class, [
                'translation_domain' => 'project_configuration',
                'choices' => [
                    'content_import.wordpress.settings.options.keep_categories.yes' => ContentImportSettings::KEEP_CATEGORIES_YES,
                    'content_import.wordpress.settings.options.keep_categories.no' => ContentImportSettings::KEEP_CATEGORIES_NO,
                ],
                'multiple' => false,
                'expanded' => true,
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ]);

            $builder->add('postAuthorsIds', HiddenType::class);

            return;
        }

        throw new \RuntimeException('Missing import source in content type!');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'import_source' => null,
        ]);
    }
}
