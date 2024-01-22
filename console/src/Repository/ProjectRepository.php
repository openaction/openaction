<?php

namespace App\Repository;

use App\Entity\Area;
use App\Entity\Community\Tag;
use App\Entity\Organization;
use App\Entity\Project;
use App\Platform\Features;
use App\Repository\Util\RepositoryUuidEncodedTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project|null findOneByBase62Uid(string $base62Uid)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    use RepositoryUuidEncodedTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function findOneByUuid(string $uuid): ?Project
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'o')
            ->leftJoin('p.organization', 'o')
            ->where('p.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findMainWebsiteProjectForOrganization(Organization $organization): ?Project
    {
        return $this->createQueryBuilder('p')
            ->where('p.modules LIKE :hasWebsiteModule')
            ->setParameter('hasWebsiteModule', '%'.Features::MODULE_WEBSITE.'%')
            ->andWhere('p.organization = :organization')
            ->setParameter('organization', $organization->getId())
            ->andWhere('p.areas IS EMPTY')
            ->andWhere('p.tags IS EMPTY')
            ->orderBy('p.createdAt', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findUuidsByOrganization(Organization $organization): array
    {
        return array_column(
            $this->createQueryBuilder('p')
                ->select('p.uuid')
                ->where('p.organization = :orga')
                ->setParameter('orga', $organization)
                ->getQuery()
                ->getArrayResult(),
            'uuid',
        );
    }

    /**
     * @return Project[]|Collection
     */
    public function findByOrganization(Organization $organization): iterable
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'a')
            ->leftJoin('p.areas', 'a')
            ->where('p.organization = :orga')
            ->setParameter('orga', $organization->getId())
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findWebsiteProjectsIds(): iterable
    {
        $data = $this->createQueryBuilder('p')
            ->select('p.id')
            ->where('p.modules LIKE :hasWebsiteModule')
            ->setParameter('hasWebsiteModule', '%'.Features::MODULE_WEBSITE.'%')
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getArrayResult()
        ;

        foreach ($data as $row) {
            yield $row['id'];
        }
    }

    public function findDomainsTokens(): array
    {
        $data = $this->createQueryBuilder('p')
            ->select('p.subDomain', 'r.name AS domain', 'p.apiToken')
            ->leftJoin('p.rootDomain', 'r')
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getArrayResult()
        ;

        $result = [];
        foreach ($data as $row) {
            $result[($row['subDomain'] ? $row['subDomain'].'.' : '').$row['domain']] = $row['apiToken'];
        }

        return $result;
    }

    /**
     * @return array<string, string>
     */
    public function createCrmNamesRegistry(Organization $organization): array
    {
        $data = $this->createQueryBuilder('p')
            ->select('p.uuid', 'p.name')
            ->where('p.organization = :organization')
            ->setParameter('organization', $organization)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getArrayResult()
        ;

        $registry = [];
        foreach ($data as $row) {
            $registry[(string) $row['uuid']] = $row['name'];
        }

        return $registry;
    }

    /**
     * @param Organization[] $organizations
     */
    public function getPartnerDashboardStats(array $organizations): array
    {
        if (!$organizations) {
            return [];
        }

        $ids = [];
        foreach ($organizations as $orga) {
            $ids[] = (int) $orga->getId();
        }

        $query = $this->_em->getConnection()->prepare('
            SELECT p.organization_id, COUNT(p.id) AS projects
            FROM projects p
            WHERE p.organization_id IN ('.implode(',', $ids).')
            GROUP BY p.organization_id
        ');

        $result = $query->executeQuery();

        $stats = [];
        while ($row = $result->fetchAssociative()) {
            $stats[$row['organization_id']] = $row['projects'];
        }

        return $stats;
    }

    public function updateAreas(Project $project, array $areasIds)
    {
        $this->_em->transactional(function () use ($project, $areasIds) {
            $metadata = $this->_em->getClassMetadata(Project::class);

            // Clear old tags
            $this->_em->getConnection()->createQueryBuilder()
                ->delete($metadata->associationMappings['areas']['joinTable']['name'])
                ->where('project_id = :project')
                ->setParameter('project', $project->getId())
                ->execute()
            ;

            // Create new tags
            $project->getTags()->clear();
            foreach ($areasIds as $areaId) {
                if ($area = $this->_em->find(Area::class, $areaId)) {
                    $project->getAreas()->add($area);
                }
            }

            $this->_em->persist($project);
            $this->_em->flush();
        });
    }

    public function updateTags(Project $project, array $tagsIds)
    {
        $this->_em->wrapInTransaction(function () use ($project, $tagsIds) {
            $metadata = $this->_em->getClassMetadata(Project::class);

            // Clear old tags
            $this->_em->getConnection()->createQueryBuilder()
                ->delete($metadata->associationMappings['tags']['joinTable']['name'])
                ->where('project_id = :project')
                ->setParameter('project', $project->getId())
                ->execute()
            ;

            // Create new tags
            $project->getTags()->clear();
            foreach ($tagsIds as $tagId) {
                if ($tag = $this->_em->find(Tag::class, $tagId)) {
                    $project->getTags()->add($tag);
                    $tag->getProjects()->add($project);

                    $this->_em->persist($tag);
                }
            }

            $this->_em->persist($project);
            $this->_em->flush();
        });
    }

    public function getExportData(): iterable
    {
        $data = $this->createQueryBuilder('p')
            ->select(
                'o.name AS organization',
                'p.subDomain',
                'd.name AS domain',
                'p.createdAt',
            )
            ->leftJoin('p.organization', 'o')
            ->leftJoin('p.rootDomain', 'd')
            ->orderBy('p.createdAt', 'ASC')
            ->where('p.modules LIKE :website')
            ->setParameter('website', '%website%')
            ->getQuery()
            ->toIterable()
        ;

        foreach ($data as $row) {
            yield [
                'organization' => $row['organization'],
                'domain' => ($row['subDomain'] ? $row['subDomain'].'.' : '').$row['domain'],
                'created_at' => $row['createdAt']->format('Y-m-d H:i'),
            ];
        }
    }

    public function countByTool(string $tool): int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p)')
            ->where('p.tools LIKE :tool')
            ->setParameter('tool', '%'.$tool.'%')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
