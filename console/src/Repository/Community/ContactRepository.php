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

    public function isEmailAlreadyUsed(Organization $orga, string $email, ?int $currentContactId = null): bool
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c.id')
            ->where('c.organization = :organization')
            ->setParameter('organization', $orga)
            ->andWhere('c.email = :email')
            ->setParameter('email', Contact::normalizeEmail($email))
            ->setMaxResults(1);

        if ($currentContactId) {
            $qb->andWhere('c.id != :current')->setParameter('current', $currentContactId);
        }

        return !empty($qb->getQuery()->getArrayResult());
    }

    public function findOneByAnyEmail(Organization $orga, string $email, bool $onlyMainEmail = false): ?Contact
    {
        $normalized = Contact::normalizeEmail($email);

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

    public function getExportData(Organization $organization, ?int $tagId = null): iterable
    {
        $sql = '
            SELECT
                c.email AS "email",
                cae.additional_emails AS "contactAdditionalEmails",
                c.profile_formal_title AS "profileFormalTitle",
                c.profile_first_name AS "profileFirstName",
                c.profile_middle_name AS "profileMiddleName",
                c.profile_last_name AS "profileLastName",
                c.birth_name AS "birthName",
                c.profile_birthdate AS "profileBirthdate",
                (CASE WHEN c.is_deceased = true THEN 1 ELSE 0 END) AS "isDeceased",
                c.profile_gender AS "profileGender",
                c.profile_nationality AS "profileNationality",
                c.birth_city AS "birthCity",
                c.birth_country_code AS "birthCountryCode",
                c.profile_company AS "profileCompany",
                c.profile_job_title AS "profileJobTitle",
                c.account_language AS "accountLanguage",
                c.contact_phone AS "contactPhone",
                c.contact_work_phone AS "contactWorkPhone",
                c.social_facebook AS "socialFacebook",
                c.social_twitter AS "socialTwitter",
                c.social_linked_in AS "socialLinkedIn",
                c.social_instagram AS "socialInstagram",
                c.social_tik_tok AS "socialTikTok",
                c.social_bluesky AS "socialBluesky",
                c.social_telegram AS "socialTelegram",
                c.social_whatsapp AS "socialWhatsapp",
                c.address_street_number AS "addressStreetNumber",
                c.address_street_line1 AS "addressStreetLine1",
                c.address_street_line2 AS "addressStreetLine2",
                c.address_zip_code AS "addressZipCode",
                c.address_city AS "addressCity",
                ac.name AS "addressCountry",
                (CASE WHEN c.settings_receive_newsletters = true THEN 1 ELSE 0 END) AS "settingsReceiveNewsletters",
                (CASE WHEN c.settings_receive_sms = true THEN 1 ELSE 0 END) AS "settingsReceiveSms",
                (CASE WHEN c.settings_receive_calls = true THEN 1 ELSE 0 END) AS "settingsReceiveCalls",
                t.tags_array AS "metadataTags",
                c.metadata_source AS "metadataSource",
                c.metadata_comment AS "metadataComment",
                c.metadata_custom_fields::text AS "metadataCustomFields",
                recruiter.email AS "recruitedBy"
            FROM community_contacts c
            LEFT JOIN LATERAL (
                SELECT string_agg(value, \', \') AS additional_emails
                FROM json_array_elements_text(c.contact_additional_emails) AS value
            ) cae ON true
            LEFT JOIN LATERAL (
                SELECT string_agg(t.name, \', \') AS tags_array
                FROM community_contacts_tags ct
                JOIN community_tags t ON t.id = ct.tag_id
                WHERE ct.contact_id = c.id
            ) t ON true
            LEFT JOIN areas ac ON c.address_country_id = ac.id
            LEFT JOIN community_contacts recruiter ON c.recruited_by_id = recruiter.id
            WHERE c.organization_id = ?
        ';

        $params = [$organization->getId()];

        if ($tagId) {
            $sql .= ' AND EXISTS (
                SELECT 1
                FROM community_contacts_tags cct
                WHERE cct.contact_id = c.id AND cct.tag_id = ?
            )';
            $params[] = $tagId;
        }

        $query = $this->_em->getConnection()->prepare($sql);

        return $query->executeQuery($params)->iterateAssociative();
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
