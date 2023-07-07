<?php

namespace App\Form\Project;

use App\Entity\Organization;
use App\Entity\User;
use App\Form\Project\Model\MoveProjectData;
use App\Repository\OrganizationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MoveProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var User $user */
        $user = $options['user'];

        /** @var Organization $current */
        $current = $options['current_organization'];

        $builder
            ->add('into', EntityType::class, [
                'required' => true,
                'class' => Organization::class,
                'query_builder' => static fn (OrganizationRepository $repo) => $repo->createMoveQueryBuilder($user, $current),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('user');
        $resolver->setAllowedTypes('user', User::class);

        $resolver->setRequired('current_organization');
        $resolver->setAllowedTypes('current_organization', Organization::class);

        $resolver->setDefaults([
            'data_class' => MoveProjectData::class,
        ]);
    }
}
