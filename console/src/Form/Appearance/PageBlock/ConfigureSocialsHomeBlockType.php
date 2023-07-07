<?php

namespace App\Form\Appearance\PageBlock;

use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class ConfigureSocialsHomeBlockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('facebook', UrlType::class, [
                'required' => false,
                'constraints' => [
                    new Length(['max' => 150, 'maxMessage' => 'console.project.appearance.homepage.socials.invalid_length']),
                    new Regex([
                        'pattern' => "/^(http(s?):\/\/)?(www\.)?facebook\.com\/(_|-|[a-z]|[0-9]|\.)+\/?$/i",
                        'message' => 'console.project.appearance.homepage.socials.invalid_facebook',
                    ]),
                ],
            ])
            ->add('twitter', UrlType::class, [
                'required' => false,
                'constraints' => [
                    new Length(['max' => 150, 'maxMessage' => 'console.project.appearance.homepage.socials.invalid_length']),
                    new Regex([
                        'pattern' => "/^(http(s?):\/\/)?(www\.)?twitter\.com\/(_|-|[a-z]|[0-9]|\.)+\/?$/i",
                        'message' => 'console.project.appearance.homepage.socials.invalid_twitter',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('project');
        $resolver->setAllowedTypes('project', Project::class);
    }
}
