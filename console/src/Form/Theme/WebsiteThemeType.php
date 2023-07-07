<?php

namespace App\Form\Theme;

use App\Entity\Organization;
use App\Form\Theme\Model\WebsiteThemeData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WebsiteThemeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = [];

        /** @var Organization $orga */
        foreach ($options['accessible_organizations'] as $orga) {
            $choices[$orga->getName()] = $orga->getId();
        }

        $builder->add('forOrganizations', ChoiceType::class, [
            'choices' => $choices,
            'multiple' => true,
            'expanded' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('accessible_organizations');
        $resolver->setAllowedTypes('accessible_organizations', 'iterable');

        $resolver->setDefaults([
            'data_class' => WebsiteThemeData::class,
        ]);
    }
}
