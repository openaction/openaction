<?php

namespace App\Form\Appearance;

use App\Entity\Project;
use App\Entity\Website\MenuItem;
use App\Form\Appearance\Model\WebsiteMenuItemData;
use App\Repository\Website\MenuItemRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WebsiteMenuItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parent', EntityType::class, [
                'class' => MenuItem::class,
                'required' => false,
                'query_builder' => static function (MenuItemRepository $repo) use ($options) {
                    $qb = $repo->createQueryBuilder('m')
                        ->where('m.project = :project')
                        ->setParameter('project', $options['project'])
                        ->andWhere('m.position = :position')
                        ->setParameter('position', $options['position'])
                        ->andWhere('m.parent IS NULL')
                        ->orderBy('m.label', 'ASC')
                    ;

                    if ($options['current_id']) {
                        $qb->andWhere('m.id != :currentId')->setParameter('currentId', $options['current_id']);
                    }

                    return $qb;
                },
            ])
            ->add('label', TextType::class, ['required' => true])
            ->add('url', TextType::class, ['required' => true])
            ->add('openNewTab', CheckboxType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WebsiteMenuItemData::class,
        ]);

        $resolver->setRequired('project');
        $resolver->setAllowedTypes('project', Project::class);

        $resolver->setRequired('position');
        $resolver->setAllowedTypes('position', 'string');

        $resolver->setRequired('current_id');
        $resolver->setAllowedTypes('current_id', ['int', 'null']);
    }
}
