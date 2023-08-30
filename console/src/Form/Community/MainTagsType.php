<?php

namespace App\Form\Community;

use App\Entity\Community\Tag;
use App\Form\Community\Model\MainTagsData;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MainTagsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tags', CollectionType::class, [
                'entry_type' => EntityType::class,
                'entry_options' => [
                    'class' => Tag::class,
                    'choices' => $options['available_tags'],
                    'required' => false,
                ],
            ])
            ->add('isProgress', CheckboxType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('available_tags');
        $resolver->setAllowedTypes('available_tags', 'iterable');

        $resolver->setDefaults([
            'data_class' => MainTagsData::class,
        ]);
    }
}
