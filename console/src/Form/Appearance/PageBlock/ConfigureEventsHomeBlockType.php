<?php

namespace App\Form\Appearance\PageBlock;

use App\Entity\Project;
use App\Entity\Website\EventCategory;
use App\Repository\Website\EventCategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigureEventsHomeBlockType extends AbstractType implements DataTransformerInterface
{
    private EventCategoryRepository $repository;

    public function __construct(EventCategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class, [
                'required' => false,
                'class' => EventCategory::class,
                'query_builder' => $this->createQueryBuilderFactory($options['project']),
            ])

            // Used to transform category ID in entity and vice-versa
            ->addModelTransformer($this)
        ;
    }

    public function createQueryBuilderFactory(Project $project)
    {
        $repository = $this->repository;

        return static function () use ($repository, $project) {
            return $repository->createQueryBuilder('c')
                ->where('c.project = :project')
                ->setParameter('project', $project)
                ->orderBy('c.name', 'ASC')
            ;
        };
    }

    public function transform($value): array
    {
        if ($value['category'] instanceof EventCategory) {
            return ['category' => $value['category']];
        }

        if (is_scalar($value['category'])) {
            return ['category' => $this->repository->find((int) $value['category'])];
        }

        return ['category' => null];
    }

    public function reverseTransform($value): array
    {
        return ['category' => $value['category']?->getId()];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('project');
        $resolver->setAllowedTypes('project', Project::class);
    }
}
