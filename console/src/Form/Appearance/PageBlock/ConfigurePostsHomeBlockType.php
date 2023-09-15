<?php

namespace App\Form\Appearance\PageBlock;

use App\Entity\Project;
use App\Entity\Website\PostCategory;
use App\Repository\Website\PostCategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurePostsHomeBlockType extends AbstractType implements DataTransformerInterface
{
    private PostCategoryRepository $repository;

    public function __construct(PostCategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class, [
                'required' => false,
                'class' => PostCategory::class,
                'query_builder' => $this->createQueryBuilderFactory($options['project']),
            ])
            ->add('label', TextType::class, [
                'required' => false,
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
        if ($value['category'] instanceof PostCategory) {
            return [
                'category' => $value['category'],
                'label' => $value['label'] ?? null,
            ];
        }

        if (is_scalar($value['category'])) {
            return [
                'category' => $this->repository->find((int) $value['category']),
                'label' => $value['label'] ?? null,
            ];
        }

        return [
            'category' => null,
            'label' => $value['label'] ?? null,
        ];
    }

    public function reverseTransform($value): array
    {
        return [
            'category' => $value['category']?->getId(),
            'label' => $value['label'] ?? null,
        ];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('project');
        $resolver->setAllowedTypes('project', Project::class);
    }
}
