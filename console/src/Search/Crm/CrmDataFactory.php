<?php

namespace App\Search\Crm;

use App\Search\CrmIndexer;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

class CrmDataFactory
{
    public function __construct(private Connection $db, private LoggerInterface $logger)
    {
    }

    public function resetIndexingTable(): void
    {
        $this->db->executeQuery('DROP TABLE IF EXISTS '.CrmIndexer::INDEXING_TABLE);

        $this->db->executeQuery('
            CREATE TABLE '.CrmIndexer::INDEXING_TABLE.' (
                organization UUID,
                uuid UUID NOT NULL UNIQUE,
                email VARCHAR(250),
                contact_additional_emails TEXT,
                contact_phone VARCHAR(50),
                parsed_contact_phone VARCHAR(50),
                contact_work_phone VARCHAR(50),
                parsed_contact_work_phone VARCHAR(50),
                profile_formal_title VARCHAR(20),
                profile_first_name VARCHAR(150),
                profile_first_name_slug VARCHAR(150),
                profile_middle_name VARCHAR(150),
                profile_middle_name_slug VARCHAR(150),
                profile_last_name VARCHAR(150),
                profile_last_name_slug VARCHAR(150),
                profile_birthdate DATE,
                profile_birthdate_int INT,
                profile_age INT,
                profile_gender VARCHAR(20),
                profile_nationality VARCHAR(2),
                profile_company VARCHAR(150),
                profile_company_slug VARCHAR(150),
                profile_job_title VARCHAR(150),
                profile_job_title_slug VARCHAR(150),
                address_street_line1 VARCHAR(150),
                address_street_line1_slug VARCHAR(150),
                address_street_line2 VARCHAR(150),
                address_street_line2_slug VARCHAR(150),
                address_zip_code VARCHAR(150),
                address_city VARCHAR(150),
                address_country VARCHAR(2),
                social_facebook VARCHAR(150),
                social_twitter VARCHAR(150),
                social_linked_in VARCHAR(150),
                social_telegram VARCHAR(150),
                social_whatsapp VARCHAR(150),
                picture VARCHAR(250),
                email_hash VARCHAR(32),
                settings_receive_newsletters BOOLEAN,
                settings_receive_sms BOOLEAN,
                settings_receive_calls BOOLEAN,
                created_at TIMESTAMP(0),
                created_at_int INT,
                status VARCHAR(1),
                area TEXT,
                tags TEXT,
                projects TEXT,
                opened_emails TEXT,
                clicked_emails TEXT
            )
        ');
    }

    public function removeContactsBatchIndexing(array $contactsUuids): void
    {
        if (!$contactsUuids) {
            return;
        }

        $this->db->executeQuery('
            DELETE FROM '.CrmIndexer::INDEXING_TABLE."
            WHERE uuid IN ('".implode("', '", $contactsUuids)."')
        ");
    }

    public function populateIndexingTableForAllOrganizations(): void
    {
        $this->doPopulateIndexingTableForFilter('ORDER BY c.organization_id ASC', []);
    }

    public function populateIndexingTableForOrganization(int $organizationId): void
    {
        $this->doPopulateIndexingTableForFilter('WHERE c.organization_id = ?', [$organizationId]);
    }

    public function populateIndexingTableForContactsBatch(array $contactsIds): void
    {
        if (!$contactsIds) {
            return;
        }

        $this->doPopulateIndexingTableForFilter(
            'WHERE c.id IN ('.implode(', ', $contactsIds).') ORDER BY c.organization_id ASC',
            []
        );
    }

    private function doPopulateIndexingTableForFilter(string $filter, array $params): void
    {
        $selectQuery = '
            SELECT
                -- Organization
                o.uuid AS organization,

                -- Details
                c.uuid, 
                replace(c.email, \'`\', \'\'\'\'),
                (
                    SELECT replace(replace(string_agg(value::text, \'✂\'), \'"\', \'\'), \'`\', \'\'\'\')
                    FROM json_array_elements(c.contact_additional_emails)
                ) AS contact_additional_emails, 
                replace(c.contact_phone, \'`\', \'\'\'\'),
                c.parsed_contact_phone, 
                replace(c.contact_work_phone, \'`\', \'\'\'\'),
                c.parsed_contact_work_phone,

                -- Profile
                replace(c.profile_formal_title, \'`\', \'\'\'\'), 
                replace(c.profile_first_name, \'`\', \'\'\'\'), 
                c.profile_first_name_slug, 
                replace(c.profile_middle_name, \'`\', \'\'\'\'), 
                c.profile_middle_name_slug, 
                replace(c.profile_last_name, \'`\', \'\'\'\'), 
                c.profile_last_name_slug,
                c.profile_birthdate,
                replace(profile_birthdate::text, \'-\', \'\')::int as profile_birthdate_int,
                extract(year from age(now(), profile_birthdate))::int as profile_age,
                c.profile_gender,
                c.profile_nationality,
                replace(c.profile_company, \'`\', \'\'\'\'),
                c.profile_company_slug, 
                replace(c.profile_job_title, \'`\', \'\'\'\'),
                c.profile_job_title_slug,

                -- Address
                replace(c.address_street_line1, \'`\', \'\'\'\'), 
                c.address_street_line1_slug, 
                replace(c.address_street_line2, \'`\', \'\'\'\'),
                c.address_street_line2_slug,
                replace(c.address_zip_code, \'`\', \'\'\'\'),
                replace(c.address_city, \'`\', \'\'\'\'),
                UPPER(country.code) AS address_country,

                -- Socials
                replace(c.social_facebook, \'`\', \'\'\'\'), 
                replace(c.social_twitter, \'`\', \'\'\'\'),
                replace(c.social_linked_in, \'`\', \'\'\'\'), 
                replace(c.social_telegram, \'`\', \'\'\'\'),
                replace(c.social_whatsapp, \'`\', \'\'\'\'),

                -- Picture
                p.pathname AS picture,
                md5(c.email) AS email_hash,

                -- Metadata
                c.settings_receive_newsletters, c.settings_receive_sms, c.settings_receive_calls, c.created_at,
                replace(to_char(c.created_at, \'YYYY-MM-DD\'), \'-\', \'\')::int as created_at_int,

                -- Status
                (CASE
                     WHEN c.account_password IS NOT NULL THEN \'m\'
                     WHEN NOT settings_receive_newsletters AND NOT settings_receive_calls AND NOT settings_receive_sms THEN \'u\'
                     ELSE \'c\'
                END) AS status,
            
                -- Areas
                (
                    WITH RECURSIVE tree (id) as
                    (
                       SELECT a.id, a.code, a.name, a.type, a.parent_id from areas a where a.id = c.area_id
                       UNION ALL
                       SELECT a.id, a.code, a.name, a.type, a.parent_id from tree, areas a where a.id = tree.parent_id
                    )
                    SELECT string_agg(concat(id, \'×\', code, \'×\', name, \'×\', type), \'✂\')
                    FROM tree
                ) AS area,
            
                -- Tags
                (
                    SELECT string_agg(concat(t.id, \'×\', t.name), \'✂\')
                    FROM community_contacts_tags ct
                    LEFT JOIN community_tags t ON ct.tag_id = t.id
                    WHERE ct.contact_id = c.id
                ) AS tags,
            
                -- Projects
                (
                    SELECT string_agg(concat(uuid, \'×\', name), \'✂\')
                    FROM (
                         -- Global projects
                         (
                             SELECT p.uuid, p.name
                             FROM projects p
                             WHERE p.organization_id = o.id
                               AND p.id NOT IN (SELECT project_id FROM projects_areas)
                               AND p.id NOT IN (SELECT project_id FROM projects_tags)
                         )
            
                         -- Local projects
                         UNION
                         (
                             SELECT p.uuid, p.name
                             FROM projects p
                             INNER JOIN projects_areas pa ON pa.project_id = p.id
                             LEFT JOIN areas paa ON pa.area_id = paa.id
                             WHERE p.organization_id = o.id
                               AND a.tree_left >= paa.tree_left
                               AND a.tree_right <= paa.tree_right
                         )
            
                         -- Thematic projects
                         UNION
                         (
                             SELECT p.uuid, p.name
                             FROM projects p
                             INNER JOIN projects_tags pt ON pt.project_id = p.id
                             WHERE p.organization_id = o.id
                               AND pt.tag_id IN (SELECT cct.tag_id FROM community_contacts_tags cct WHERE cct.contact_id = c.id)
                         )
                     ) p
                ) as projects,
            
                -- Opened emails
                (
                    SELECT string_agg(eo.uuid::text, \'✂\')
                    FROM community_emailing_campaigns_messages ceo
                    LEFT JOIN community_emailing_campaigns eo ON eo.id = ceo.campaign_id
                    WHERE ceo.contact_id = c.id
                    AND ceo.opened = true
                ) as opened_emails,
            
                -- Clicked emails
                (
                    SELECT string_agg(ec.uuid::text, \'✂\')
                    FROM community_emailing_campaigns_messages cec
                    LEFT JOIN community_emailing_campaigns ec ON ec.id = cec.campaign_id
                    WHERE cec.contact_id = c.id
                    AND cec.clicked = true
                ) as clicked_emails
            FROM community_contacts c
            LEFT JOIN areas a ON c.area_id = a.id
            LEFT JOIN areas country ON c.address_country_id = country.id
            LEFT JOIN uploads p ON c.picture_id = p.id
            LEFT JOIN organizations o ON c.organization_id = o.id
        ';

        $indexingTable = CrmIndexer::INDEXING_TABLE;

        $sql = "
            INSERT INTO $indexingTable (
               -- Organization
                organization, 
                
                -- Details
                uuid, 
                email,
                contact_additional_emails, 
                contact_phone, 
                parsed_contact_phone, 
                contact_work_phone, 
                parsed_contact_work_phone, 
                
                -- Profile
                profile_formal_title, 
                profile_first_name,
                profile_first_name_slug, 
                profile_middle_name, 
                profile_middle_name_slug,
                profile_last_name,
                profile_last_name_slug,
                profile_birthdate, 
                profile_birthdate_int, 
                profile_age,
                profile_gender,
                profile_nationality, 
                profile_company, 
                profile_company_slug,
                profile_job_title,
                profile_job_title_slug, 
                
                -- Address
                address_street_line1, 
                address_street_line1_slug, 
                address_street_line2, 
                address_street_line2_slug, 
                address_zip_code, 
                address_city, 
                address_country, 
                
                -- Socials
                social_facebook,
                social_twitter,
                social_linked_in,
                social_telegram,
                social_whatsapp,
                
                -- Picture
                picture,
                email_hash,
                
                -- Metadata
                settings_receive_newsletters,
                settings_receive_sms,
                settings_receive_calls,
                created_at,
                created_at_int,
                
                -- Status
                status,
                
                -- Relationships
                area,
                tags,
                projects,
                opened_emails,
                clicked_emails
            )
            $selectQuery
            $filter
            ON CONFLICT (uuid) DO NOTHING
        ";

        $this->logger->info('Dumping indexing table', [
            'query' => $sql,
            'params' => $params,
        ]);

        // Execute insert ignoring conflicts as they most likely come from another indexing happening in parallel,
        // meaning contact data is the same
        try {
            $this->db->executeQuery($sql, $params);
        } catch (\Throwable $e) {
            $this->logger->error('Dumping indexing table failed', [
                'exception' => $e,
                'query' => $sql,
                'params' => $params,
            ]);

            throw $e;
        }
    }
}
