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
        if (ContentImportSettings::IMPORT_SOURCE_WORDPRESS === $options['import_source']) {
            $builder->add('postSaveStatus', ChoiceType::class, [
                'translation_domain' => 'project_configuration',
                'choices' => [
                    'content_import.settings.wordpress.post_save_status.options.as_draft' => ContentImportSettings::POST_STATUS_SAVE_AS_DRAFT,
                    'content_import.settings.wordpress.post_save_status.options.as_original' => ContentImportSettings::POST_STATUS_SAVE_AS_ORIGINAL,
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
