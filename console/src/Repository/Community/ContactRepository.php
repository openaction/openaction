<?php

namespace App\Repository\Community;

use App\Api\Model\ContactUpdateEmailApiData;
use App\Entity\Community\Contact;
use App\Entity\Community\Tag;
use App\Entity\Organization;
use App\Entity\Project;
use App\Repository\Util\GridSearchRepositoryTrait;
use App\Repository\Util\RepositoryUuidEncodedTrait;
use App\Util\Date;
use App\Util\Uid;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @method Contact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contact|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contact[]    findAll()
 * @method Contact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactRepository extends ServiceEntityRepository
{
    use RepositoryUuidEncodedTrait;
    use GridSearchRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    public function iterateAll(): iterable
    {
        return $this->createQueryBuilder('c')->getQuery()->toIterable();
    }

    public function findOneByMainEmail(Organization $orga, string $email): ?Contact
    {
        return $this->findOneBy([
            'organization' => $orga,
            'email' => Contact::normalizeEmail($email),
        ]);
    }

    public function findOneByAnyEmail(Organization $orga, string $email, bool $onlyMainEmail = false): ?Contact
    {
        $normalized = Contact::normalizeEmail(trim($email));

        if ($contact = $this->findOneBy(['organization' => $orga, 'email' => $normalized])) {
            return $contact;
        }

        if ($onlyMainEmail) {
            return null;
        }

        $id = $this->_em->getConnection()->executeQuery(
            'SELECT id FROM community_contacts WHERE contact_additional_emails::text LIKE ? AND organization_id = ? LIMIT 1',
            ['%'.$normalized.'%', $orga->getId()]
        )->fetchNumeric();

        return $id ? $this->find($id[0]) : null;
    }

    public function findAdminCommunityTotals(): array
    {
        $query = $this->_em->getConnection()->prepare('
            SELECT 
               COUNT(*) AS contacts,
               COUNT(CASE WHEN account_password IS NOT NULL THEN 1 END) AS members,
               COUNT(CASE WHEN settings_receive_newsletters THEN 1 END) AS newsletter_subscribers,
               COUNT(CASE WHEN settings_receive_sms THEN 1 END) AS sms_subscribers
            FROM community_contacts
        ');

        return $query->executeQuery([])->fetchAssociative();
    }

    public function findAdminCommunityGrowth(\DateTime $startDate): iterable
    {
        $query = $this->_em->getConnection()->prepare("
            SELECT
               TO_TIMESTAMP(FLOOR((EXTRACT('epoch' from created_at) / ".Date::OneDay->value.')) * '.Date::OneDay->value.') as period,
               COUNT(*) AS new_contacts,
               COUNT(CASE WHEN account_password IS NOT NULL THEN 1 END) AS new_members
            FROM community_contacts
            WHERE created_at >= ? AND created_at <= ?
            GROUP BY period
            ORDER BY period
        ');

        $result = $query->executeQuery([
            $startDate->format('Y-m-d H:i:s'),
            (new \DateTime())->format('Y-m-d H:i:s'),
        ]);

        while ($row = $result->fetchAssociative()) {
            yield $row;
        }
    }

    public function findAdminCommunityOrganizations(): \Generator
    {
        $query = $this->_em->getConnection()->prepare('
            SELECT o.name, COUNT(*) AS value
            FROM community_contacts c
            LEFT JOIN organizations o ON o.id = c.organization_id
            GROUP BY o.name
            ORDER BY value DESC
        ');

        $result = $query->executeQuery([]);

        while ($row = $result->fetchAssociative()) {
            yield $row['name'] => $row['value'];
        }
    }

    /**
     * @param Tag[] $tagsIds
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function clearTags(Contact $contact, array $tagsIds)
    {
        if (!$tagsIds) {
            return;
        }

        $metadata = $this->_em->getClassMetadata(Contact::class);

        $qb = $this->_em->getConnection()->createQueryBuilder();
        $qb->delete($metadata->associationMappings['metadataTags']['joinTable']['name'])
            ->where('contact_id = :contact')
            ->setParameter('contact', $contact->getId())
            ->andWhere($qb->expr()->in('tag_id', $tagsIds))
            ->executeStatement()
        ;
    }

    public function updateTags(Contact $contact, array $tagsData)
    {
        $this->_em->wrapInTransaction(function () use ($contact, $tagsData) {
            $metadata = $this->_em->getClassMetadata(Contact::class);

            // Clear old tags
            $this->_em->getConnection()->createQueryBuilder()
                ->delete($metadata->associationMappings['metadataTags']['joinTable']['name'])
                ->where('contact_id = :contact')
                ->setParameter('contact', $contact->getId())
                ->executeStatement()
            ;

            // Create new tags
            $contact->getMetadataTags()->clear();
            foreach ($tagsData as $tagData) {
                $tag = null;
                if (isset($tagData['id'])) {
                    $tag = $this->_em->getRepository(Tag::class)->findOneBy([
                        'id' => $tagData['id'],
                        'organization' => $contact->getOrganization(),
                    ]);
                } elseif (isset($tagData['name']) && '' !== trim($tagData['name'])) {
                    $tag = new Tag($contact->getOrganization(), trim($tagData['name']));
                }

                if ($tag) {
                    $contact->getMetadataTags()->add($tag);
                    $tag->getContacts()->add($contact);

                    $this->_em->persist($tag);
                }
            }

            $this->_em->persist($contact);
            $this->_em->flush();
        });
    }

    public function getExportData(Organization $organization, int $tagId = null): iterable
    {
        $db = $this->_em->getConnection();

        // Get tags list
        $tags = $db->executeQuery('
            SELECT t.name FROM community_tags t WHERE t.organization_id = ? ORDER BY t.name
        ', [$organization->getId()])->fetchAllAssociative();

        // Get projects list
        $projects = $db->executeQuery('
            SELECT 
               p.name,
               (
                    SELECT STRING_AGG(t.name, \'~|~\') 
                    FROM projects_tags pt 
                    LEFT JOIN community_tags t ON t.id = pt.tag_id 
                    WHERE pt.project_id = p.id
               ) AS tags,
               (
                    SELECT STRING_AGG(CONCAT(CONCAT(a.tree_left, \';\'), a.tree_right), \'~|~\') 
                    FROM projects_areas pa
                    LEFT JOIN areas a ON a.id = pa.area_id 
                    WHERE pa.project_id = p.id
               ) AS areas
            FROM projects p
            WHERE p.organization_id = ?
            ORDER BY p.name
        ', [$organization->getId()])->fetchAllAssociative();

        foreach ($projects as $key => $project) {
            $areas = [];
            foreach ($project['areas'] ? explode('~|~', $project['areas']) : [] as $area) {
                $area = explode(';', $area);
                $areas[] = ['left' => (int) $area[0], 'right' => (int) $area[1]];
            }

            $projects[$key]['areas'] = $areas;
            $projects[$key]['tags'] = $project['tags'] ? explode('~|~', $project['tags']) : [];
        }

        $sql = '
            SELECT DISTINCT ON (c.email) 
                c.email,
                c.uuid AS id,
                a.name AS area,
                a.tree_left AS area_tree_left,
                a.tree_right AS area_tree_right,
                c.profile_formal_title,
                c.profile_first_name,
                c.profile_middle_name,
                c.profile_last_name,
                c.profile_birthdate,
                c.profile_company,
                c.profile_job_title,
                c.contact_phone,
                c.contact_work_phone,
                c.parsed_contact_phone,
                c.parsed_contact_work_phone,
                c.social_facebook,
                c.social_twitter,
                c.social_linked_in,
                c.social_telegram,
                c.social_whatsapp,
                c.address_street_line1,
                c.address_street_line2,
                c.address_zip_code,
                c.address_city,
                ac.name AS address_country,
                (CASE WHEN c.account_password IS NOT NULL THEN 1 ELSE 0 END) AS is_member,
                (CASE WHEN c.settings_receive_newsletters = true THEN 1 ELSE 0 END) AS settings_receive_newsletters,
                (CASE WHEN c.settings_receive_sms = true THEN 1 ELSE 0 END) AS settings_receive_sms,
                (CASE WHEN c.settings_receive_calls = true THEN 1 ELSE 0 END) AS settings_receive_calls,
                t.tags_array AS metadata_tags,
                c.metadata_comment, 
                c.created_at
            FROM community_contacts c
            LEFT JOIN (
                SELECT ct.contact_id AS id, string_agg(t.name, \', \') AS tags_array
                FROM community_contacts_tags ct
                JOIN community_tags t ON t.id = ct.tag_id
                GROUP BY ct.contact_id
            ) t USING (id)
            LEFT JOIN json_array_elements_text(c.contact_additional_emails) AS cae ON true
            LEFT JOIN areas a ON c.area_id = a.id
            LEFT JOIN areas ac ON c.address_country_id = ac.id
            WHERE c.organization_id = ?
        ';

        $params = [$organization->getId()];

        // Filter by tag if requested
        if ($tagId) {
            $sql .= ' AND c.id IN (SELECT contact_id FROM community_contacts_tags WHERE tag_id = ?)';
            $params[] = $tagId;
        }

        // Create the contacts list
        $query = $this->_em->getConnection()->prepare($sql);

        foreach ($query->executeQuery($params)->iterateAssociative() as $row) {
            // Add individual tag columns
            $contactTags = array_map('trim', explode(',', (string) $row['metadata_tags']));
            foreach ($tags as $tag) {
                $row['Tag '.$tag['name']] = in_array($tag['name'], $contactTags, true) ? 1 : 0;
            }

            // Add individual sub-projects
            foreach ($projects as $project) {
                // Global project: always true
                if (!$project['tags'] && !$project['areas']) {
                    $row['Project '.$project['name']] = 1;

                    continue;
                }

                // Thematic project
                if ($project['tags']) {
                    $isInProject = false;
                    foreach ($project['tags'] as $projectTag) {
                        if (in_array($projectTag, $contactTags, true)) {
                            $isInProject = true;
                            break;
                        }
                    }

                    $row['Project '.$project['name']] = $isInProject ? 1 : 0;

                    continue;
                }

                // Local project
                $isInProject = false;
                foreach ($project['areas'] as $projectArea) {
                    if ($projectArea['left'] >= $row['area_tree_left'] && $projectArea['right'] <= $row['area_tree_right']) {
                        $isInProject = true;
                        break;
                    }
                }

                $row['Project '.$project['name']] = $isInProject ? 1 : 0;
            }

            unset($row['area_tree_left'], $row['area_tree_right']);

            yield $row;
        }
    }

    public function updateEmail(ContactUpdateEmailApiData $contactUpdateApiData)
    {
        $contact = $contactUpdateApiData->getContact();
        $contact->changeEmail($contactUpdateApiData->newEmail);

        $this->_em->persist($contact);
        $this->_em->flush();
    }

    public function unregister(Contact $contact)
    {
        $contact->applyUnregister();
        $this->_em->persist($contact);
        $this->_em->flush();
    }

    public function removeBatch(array $contactsUuids): void
    {
        if (!$contactsUuids) {
            return;
        }

        $this->_em->getConnection()->executeStatement('
            DELETE FROM community_contacts c WHERE c.uuid IN (\''.implode('\', \'', $contactsUuids).'\')
        ');
    }

    public function hasContactInOrganization(string $email, Contact $contact)
    {
        return (bool) $this->createQueryBuilder('c')
            ->select('c.id')
            ->where('c.email = :email')
            ->setParameter('email', $email)
            ->andWhere('c.id <> :id')
            ->setParameter('id', $contact->getId())
            ->andWhere('c.organization = :orga')
            ->setParameter('orga', $contact->getOrganization())
            ->getQuery()
            ->execute()
        ;
    }

    public function findOneByBase62Uid(string $base62Uid): ?Contact
    {
        if (!$uuid = Uid::fromBase62($base62Uid)) {
            return null;
        }

        /** @var Contact $contact */
        $contact = $this->createQueryBuilder('c')
            ->join('c.organization', 'o')
            ->join('o.projects', 'p')
            ->andWhere('c.uuid = :uuid')
            ->setParameter('uuid', $uuid->toRfc4122())
            ->getQuery()
            ->getOneOrNullResult();

        if (null === $contact) {
            return null;
        }

        $this->refreshContactSettings($contact);

        return $contact;
    }

    public function refreshContactSettings(Contact $contact): void
    {
        $projects = $contact->getOrganization()->getProjects();
        $freshSettingsByProject = [];

        // Populate settings for projects containing the contact
        /** @var Project $project */
        foreach ($projects as $project) {
            if (!$contact->isInProject($project)) {
                continue;
            }

            $projectId = Uid::toBase62(Uuid::fromString($project->getUuid()));

            foreach ($contact->getSettingsByProject() as $settingByProject) {
                // For projects with configuration, apply global settings while keeping local ones
                if ($projectId === $settingByProject['projectId']) {
                    $settingByProject['projectName'] = $project->getName();

                    if (true === $contact->hasSettingsReceiveNewsletters()) {
                        $settingByProject['settingsReceiveNewsletters'] = true;
                    }

                    if (true === $contact->hasSettingsReceiveCalls()) {
                        $settingByProject['settingsReceiveCalls'] = true;
                    }

                    if (true === $contact->hasSettingsReceiveSms()) {
                        $settingByProject['settingsReceiveSms'] = true;
                    }

                    $freshSettingsByProject[$projectId] = $settingByProject;

                    continue 2;
                }
            }

            // By default, use global settings
            $freshSettingsByProject[$projectId] = [
                'projectName' => $project->getName(),
                'projectId' => $projectId,
                'settingsReceiveNewsletters' => $contact->hasSettingsReceiveNewsletters(),
                'settingsReceiveSms' => $contact->hasSettingsReceiveSms(),
                'settingsReceiveCalls' => $contact->hasSettingsReceiveCalls(),
            ];
        }

        $contact->setSettingsByProject(array_values($freshSettingsByProject));
    }
}
