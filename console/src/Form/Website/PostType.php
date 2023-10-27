<?php

namespace App\Form\Website;

use App\Form\VideoUrlType;
use App\Form\Website\Model\PostData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', HiddenType::class, ['required' => false])
            ->add('description', HiddenType::class, ['required' => false])
            ->add('quote', HiddenType::class, ['required' => false])
            ->add('author', HiddenType::class, ['required' => false])
            ->add('externalUrl', HiddenType::class, ['required' => false])
            ->add('video', VideoUrlType::class, ['required' => false])
            ->add('content', HiddenType::class, ['required' => false])
            ->add('publishedAt', HiddenType::class, ['required' => false])
            ->add('categories', HiddenType::class, ['required' => false])
            ->add('onlyForMembers', HiddenType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PostData::class,
            'validation_groups' => ['Default'],
            'csrf_protection' => false,
        ]);
    }
}
