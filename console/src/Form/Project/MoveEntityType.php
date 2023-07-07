<?php

namespace App\Form\Project;

use App\Entity\Project;
use App\Entity\User;
use App\Form\Project\Model\MoveEntityData;
use App\Repository\ProjectRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MoveEntityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var User $user */
        $user = $options['user'];

        /** @var string $permission */
        $permission = $options['permission'];

        /** @var Project $currentProject */
        $currentProject = $options['current_project'];

        $builder
            ->add('into', EntityType::class, [
                'required' => true,
                'class' => Project::class,
                'choice_label' => static function (Project $project) {
                    return $project->getOrganization()->getName().' / '.$project->getName();
                },
                'query_builder' => static function (ProjectRepository $repository) use ($user, $permission, $currentProject) {
                    // Always add a 0 to ensure no errors will be thrown by IN statements (while returning no result)
                    $adminOrgasIds = [0];
                    $memberProjectsUuids = [];

                    foreach ($user->getMemberships() as $membership) {
                        if ($membership->isAdmin()) {
                            $adminOrgasIds[] = $membership->getOrganization()->getId();
                        } else {
                            $projectsPermissions = $membership->getProjectsPermissions();
                            foreach ($projectsPermissions->getConfiguredProjectsIds() as $projectUuid) {
                                if ($projectsPermissions->hasPermission($projectUuid, $permission)) {
                                    $memberProjectsUuids[] = $projectUuid;
                                }
                            }
                        }
                    }

                    $qb = $repository->createQueryBuilder('p');

                    $where = $qb->expr()->orX();

                    // Admin orgas => all projects allowed
                    $where->add($qb->expr()->in('o.id', $adminOrgasIds));

                    // Member orgas => projects with permission allowed
                    if ($memberProjectsUuids) {
                        $where->add($qb->expr()->in('p.uuid', $memberProjectsUuids));
                    }

                    return $qb
                        ->select('p', 'o')
                        ->leftJoin('p.organization', 'o')
                        ->where($where)
                        ->andWhere('p.id != :currentProject')
                        ->setParameter('currentProject', $currentProject->getId())
                        ->orderBy('o.name', 'ASC')
                        ->addOrderBy('p.name', 'ASC')
                    ;
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('user');
        $resolver->setAllowedTypes('user', User::class);

        $resolver->setRequired('permission');
        $resolver->setAllowedTypes('permission', 'string');

        $resolver->setRequired('current_project');
        $resolver->setAllowedTypes('current_project', Project::class);

        $resolver->setDefaults([
            'data_class' => MoveEntityData::class,
        ]);
    }
}
