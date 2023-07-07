<?php

namespace App\Form\Appearance;

use App\Entity\Theme\WebsiteTheme;
use App\Form\Appearance\Model\WebsiteThemeData;
use App\Platform\Fonts;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WebsiteThemeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('theme', EntityType::class, [
                'class' => WebsiteTheme::class,
                'query_builder' => $options['themes_query_builder'],
                'choice_label' => static fn (WebsiteTheme $theme) => $theme->getName()['fr'],
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('appearancePrimary', TextType::class)
            ->add('appearanceSecondary', TextType::class)
            ->add('appearanceThird', TextType::class)
            ->add('fontTitle', ChoiceType::class, [
                'choice_translation_domain' => false,
                'choices' => array_keys(Fonts::FAMILIES),
                'choice_label' => fn ($choice) => $choice,
            ])
            ->add('fontText', ChoiceType::class, [
                'choice_translation_domain' => false,
                'choices' => array_keys(Fonts::FAMILIES),
                'choice_label' => fn ($choice) => $choice,
            ])
            ->add('mainIntroPosition', ChoiceType::class, [
                'translation_domain' => 'project_appearance',
                'choices' => [
                    'theme.options.positions.choices.left' => 'left',
                    'theme.options.positions.choices.center' => 'center',
                    'theme.options.positions.choices.right' => 'right',
                ],
            ])
            ->add('mainIntroOverlay', CheckboxType::class, ['required' => false])
            ->add('animateElements', CheckboxType::class, ['required' => false])
            ->add('animateLinks', CheckboxType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WebsiteThemeData::class,
        ]);

        $resolver->setRequired('themes_query_builder');
        $resolver->setAllowedTypes('themes_query_builder', QueryBuilder::class);
    }
}
