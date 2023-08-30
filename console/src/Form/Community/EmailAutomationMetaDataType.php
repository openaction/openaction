<?php

namespace App\Form\Community;

use App\Entity\Community\EmailAutomation;
use App\Entity\Organization;
use App\Entity\Website\Form;
use App\Form\Community\Model\EmailAutomationMetaData;
use App\Repository\Website\FormRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailAutomationMetaDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('trigger', ChoiceType::class, [
                'multiple' => false,
                'expanded' => false,
                'translation_domain' => 'organization_community',
                'choices' => [
                    'automation.metadata.automation.trigger.contact_created' => EmailAutomation::TRIGGER_NEW_CONTACT,
                    'automation.metadata.automation.trigger.form_answered' => EmailAutomation::TRIGGER_NEW_FORM_ANSWER,
                ],
            ])
            ->add('formFilter', EntityType::class, [
                'class' => Form::class,
                'required' => false,
                'query_builder' => static function (FormRepository $repository) use ($options) {
                    return $repository->createQueryBuilder('f')
                        ->leftJoin('f.project', 'p')
                        ->where('p.organization = :organization')
                        ->setParameter('organization', $options['organization'])
                        ->orderBy('p.name', 'ASC')
                        ->orderBy('f.title', 'ASC');
                },
                'choice_label' => static function (Form $form) {
                    return $form->getProject()?->getName().' - '.$form->getTitle();
                },
                'placeholder' => 'automation.metadata.automation.trigger.form_filter_placeholder',
                'translation_domain' => 'organization_community',
            ])
            ->add('subject', TextType::class)
            ->add('preview', TextType::class, ['required' => false])
            ->add('fromEmail', EmailType::class)
            ->add('fromName', TextType::class, ['required' => false])
            ->add('toEmailType', ChoiceType::class, [
                'multiple' => false,
                'expanded' => true,
                'translation_domain' => 'organization_community',
                'choices' => [
                    'automation.metadata.to.toEmailType.everyone' => 'everyone',
                    'automation.metadata.to.toEmailType.specific' => 'specific',
                ],
            ])
            ->add('toEmail', EmailType::class, ['required' => false])
            ->add('typeFilter', ChoiceType::class, [
                'multiple' => false,
                'expanded' => true,
                'translation_domain' => 'organization_community',
                'choices' => [
                    'automation.metadata.to.typeFilter.everyone' => '',
                    'automation.metadata.to.typeFilter.contact' => EmailAutomation::TYPE_CONTACT,
                    'automation.metadata.to.typeFilter.member' => EmailAutomation::TYPE_MEMBER,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['organization']);
        $resolver->setAllowedTypes('organization', [Organization::class]);

        $resolver->setDefaults([
            'data_class' => EmailAutomationMetaData::class,
        ]);
    }
}
