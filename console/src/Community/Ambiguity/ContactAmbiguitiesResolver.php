<?php

namespace App\Community\Ambiguity;

use App\Entity\Organization;
use Doctrine\DBAL\Connection;

class ContactAmbiguitiesResolver
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Persist and return the list of ambiguities detected, in the form array<[orga ID, oldest ID, newest ID]>.
     *
     * @param Organization|null $organization Resolve only for the given organization if provided (for all otherwise).
     *
     * @return array<array<int>>
     */
    public function resolveAmbiguities(Organization $organization = null): array
    {
        $similarities = [
            $this->resolveFullNameSimilarities($organization),
            $this->resolveParsedPhoneSimilarities($organization),
        ];

        /** @var \DateTime[] $contactCreationDates */
        $contactCreationDates = [];

        // Flatten all sources of similarities
        $similarIds = [];
        foreach ($similarities as $fieldSimilarities) {
            foreach ($fieldSimilarities as $orgaId => $values) {
                foreach ($values as $contacts) {
                    foreach ($contacts as $contactA) {
                        if (!isset($contactCreationDates[$contactA['id']])) {
                            $contactCreationDates[$contactA['id']] = new \DateTime($contactA['created_at']);
                        }

                        foreach ($contacts as $contactB) {
                            if (!isset($contactCreationDates[$contactB['id']])) {
                                $contactCreationDates[$contactB['id']] = new \DateTime($contactB['created_at']);
                            }

                            if ($contactA['id'] !== $contactB['id']) {
                                $similarIds[$orgaId][$contactA['id']][$contactB['id']] = true;
                            }
                        }
                    }
                }
            }
        }

        // Find already ignored ambiguities
        $ignoredAmbiguities = $this->findIgnoredAmbiguities($organization);

        // Create ambiguities only for oldest => newest and only if not already ignored
        $ambiguities = [];
        foreach ($similarIds as $orgaId => $similarContacts) {
            foreach ($similarContacts as $contactId => $similarContactsIds) {
                foreach ($similarContactsIds as $similarId => $v) {
                    // If the ambiguity is already ignored, continue
                    if (isset($ignoredAmbiguities[$orgaId][$contactId][$similarId])) {
                        continue;
                    }

                    // If the reference contact is newest, continue (the reverse similarity will match)
                    if ($contactCreationDates[$contactId] > $contactCreationDates[$similarId]) {
                        continue;
                    }

                    // If the creation date is the same (import, fixtures, ...), use the lowest ID
                    if ($contactId > $similarId
                        && $contactCreationDates[$contactId]->getTimestamp() === $contactCreationDates[$similarId]->getTimestamp()) {
                        continue;
                    }

                    // Otherwise, create the ambiguity
                    $ambiguities[] = [$orgaId, $contactId, $similarId];
                }
            }
        }

        return $ambiguities;
    }

    public function persistResolvedAmbiguities(array $ambiguities, Organization $organization = null)
    {
        // Clean untreated ambiguities and persist newly detected ones
        $sets = [];
        foreach ($ambiguities as $ambiguity) {
            $sets[] = sprintf(
                '(nextval(\'community_ambiguities_id_seq\'), %s, %s, %s, NULL)',
                $ambiguity[0],
                $ambiguity[1],
                $ambiguity[2],
            );
        }

        $this->db->transactional(static function (Connection $db) use ($sets, $organization) {
            // Delete untreated ambiguities
            $db->executeStatement('
                DELETE FROM community_ambiguities
                WHERE ignored_at IS NULL '.($organization ? 'AND organization_id = '.$organization->getId() : '')
            );

            // Insert the new ones
            if ($sets) {
                $db->executeStatement('
                    INSERT INTO community_ambiguities (id, organization_id, oldest_id, newest_id, ignored_at)
                    VALUES '.implode(', ', $sets)
                );
            }
        });
    }

    private function resolveFullNameSimilarities(Organization $organization = null): array
    {
        $result = $this->db->executeQuery('
            SELECT c.id AS contact_id, c.organization_id, c.created_at,
                   lower(trim(concat(c.profile_first_name, concat(\' \', c.profile_last_name)))) as full_name
            FROM community_contacts c
            JOIN (
                SELECT organization_id, full_name, count(*)
                FROM (
                    SELECT organization_id, lower(trim(concat(profile_first_name, concat(\' \', profile_last_name)))) AS full_name
                    FROM community_contacts
                ) full_name_ambiguities
                WHERE full_name != \'\'
                '.($organization ? 'AND organization_id = '.$organization->getId() : '').'
                GROUP BY organization_id, full_name
                HAVING COUNT(*) > 1
            ) fna
            ON lower(trim(concat(c.profile_first_name, concat(\' \', c.profile_last_name)))) = fna.full_name
            AND c.organization_id = fna.organization_id
            ORDER BY c.created_at ASC
        ');

        $ids = [];
        foreach ($result->iterateAssociative() as $row) {
            $ids[$row['organization_id']][$row['full_name']][] = [
                'id' => $row['contact_id'],
                'created_at' => $row['created_at'],
            ];
        }

        return $ids;
    }

    private function resolveParsedPhoneSimilarities(Organization $organization = null): array
    {
        $result = $this->db->executeQuery('
            SELECT c.id AS contact_id, c.organization_id, c.created_at, c.parsed_contact_phone
            FROM community_contacts c
            JOIN (
                SELECT organization_id, parsed_contact_phone, count(*)
                FROM community_contacts
                WHERE parsed_contact_phone IS NOT NULL
                '.($organization ? 'AND organization_id = '.$organization->getId() : '').'
                GROUP BY organization_id, parsed_contact_phone
                HAVING COUNT(*) > 1
            ) pcpa
            ON c.parsed_contact_phone = pcpa.parsed_contact_phone
            AND c.organization_id = pcpa.organization_id
            ORDER BY c.created_at ASC
        ');

        $ids = [];
        foreach ($result->iterateAssociative() as $row) {
            $ids[$row['organization_id']][$row['parsed_contact_phone']][] = [
                'id' => $row['contact_id'],
                'created_at' => $row['created_at'],
            ];
        }

        return $ids;
    }

    private function findIgnoredAmbiguities(Organization $organization = null): array
    {
        $result = $this->db->executeQuery('
            SELECT a.organization_id, a.oldest_id, a.newest_id
            FROM community_ambiguities a
            WHERE a.ignored_at IS NOT NULL
            '.($organization ? 'AND a.organization_id = '.$organization->getId() : '')
        );

        $data = [];
        foreach ($result->iterateAssociative() as $row) {
            $data[$row['organization_id']][$row['oldest_id']][$row['newest_id']] = true;
        }

        return $data;
    }
}
