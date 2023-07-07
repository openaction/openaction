<?php

namespace App\Repository\Website;

use App\Entity\Project;
use App\Entity\Website\Form;
use App\Entity\Website\FormBlock;
use App\Form\Website\Model\FormData;
use App\Repository\Util\RepositoryUuidEncodedTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Form|null find($id, $lockMode = null, $lockVersion = null)
 * @method Form|null findOneBy(array $criteria, array $orderBy = null)
 * @method Form|null findOneByBase62Uid(string $base62Uid)
 * @method Form[]    findAll()
 * @method Form[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormRepository extends ServiceEntityRepository
{
    use RepositoryUuidEncodedTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Form::class);
    }

    public function getPaginator(Project $project, int $currentPage, int $limit = 10): Paginator
    {
        $qb = $this->createQueryBuilder('f')
            ->select('f')
            ->leftJoin('f.phoningCampaign', 'pc')
            ->where('f.project = :project')
            ->setParameter('project', $project->getId())
            ->andWhere('pc.name IS NULL')
            ->orderBy('f.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult(($currentPage - 1) * $limit)
        ;

        return new Paginator($qb->getQuery(), true);
    }

    /**
     * @return Form[]
     */
    public function getApiForms(Project $project): iterable
    {
        $qb = $this->createQueryBuilder('f')
            ->select('f', 'b')
            ->leftJoin('f.blocks', 'b')
            ->leftJoin('f.phoningCampaign', 'pc')
            ->where('f.project = :project')
            ->setParameter('project', $project->getId())
            ->andWhere('pc.name IS NULL')
            ->andWhere('f.onlyForMembers = FALSE')
            ->orderBy('f.weight', 'ASC')
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Form[]
     */
    public function getMembersApiForms(Project $project, int $currentPage, int $limit = 12): Paginator
    {
        $qb = $this->createQueryBuilder('f')
            ->select('f', 'b')
            ->leftJoin('f.blocks', 'b')
            ->leftJoin('f.phoningCampaign', 'pc')
            ->where('f.project = :project')
            ->setParameter('project', $project->getId())
            ->andWhere('pc.name IS NULL')
            ->andWhere('f.onlyForMembers = TRUE')
            ->orderBy('f.weight', 'ASC')
            ->setMaxResults($limit)
            ->setFirstResult(($currentPage - 1) * $limit)
        ;

        return new Paginator($qb->getQuery(), true);
    }

    public function saveBlocks(Form $form, FormData $data)
    {
        $this->_em->transactional(function () use ($form, $data) {
            $this->_em->createQueryBuilder()
                ->delete(FormBlock::class, 'b')
                ->where('b.form = :form')
                ->setParameter('form', $form->getId())
                ->getQuery()
                ->execute()
            ;

            $form->getBlocks()->clear();

            $weight = 1;
            foreach ($data->blocks as $blockData) {
                $block = FormBlock::createFromData($form, $blockData, $weight);

                $form->getBlocks()->add($block);
                $this->_em->persist($block);

                ++$weight;
            }

            $this->_em->persist($form);
            $this->_em->flush();
        });
    }
}
