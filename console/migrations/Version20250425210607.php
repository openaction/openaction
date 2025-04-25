<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250328210607 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // Drop the existing view
        $this->addSql('DROP VIEW indexing_view');

        // Recreate the view with updated status logic
        $this->addSql(<<<EOT
CREATE VIEW indexing_view AS
SELECT
    -- Organization
    o.uuid AS organization,

    -- Details
    c.uuid,
    replace(c.email, '`', '''') AS email,
    (
        SELECT replace(replace(string_agg(value::text, '✂'), '"', ''), '`', '''')
        FROM json_array_elements(c.contact_additional_emails)
    ) AS contact_additional_emails,
    replace(c.contact_phone, '`', '''') AS contact_phone,
    c.parsed_contact_phone,
    replace(c.contact_work_phone, '`', '''') AS contact_work_phone,
    c.parsed_contact_work_phone,

    -- Profile
    replace(c.profile_formal_title, '`', '''') AS profile_formal_title,
    replace(c.profile_first_name, '`', '''') AS profile_first_name,
    c.profile_first_name_slug,
    replace(c.profile_middle_name, '`', '''') AS profile_middle_name,
    c.profile_middle_name_slug,
    replace(c.profile_last_name, '`', '''') AS profile_last_name,
    c.profile_last_name_slug,
    c.profile_birthdate,
    replace(profile_birthdate::text, '-', '')::int as profile_birthdate_int,
    extract(year from age(now(), profile_birthdate))::int as profile_age,
    c.profile_gender,
    c.profile_nationality,
    replace(c.profile_company, '`', '''') AS profile_company,
    c.profile_company_slug,
    replace(c.profile_job_title, '`', '''') AS profile_job_title,
    c.profile_job_title_slug,

    -- Address
    replace(c.address_street_line1, '`', '''') AS address_street_line1,
    c.address_street_line1_slug,
    replace(c.address_street_line2, '`', '''') AS address_street_line2,
    c.address_street_line2_slug,
    replace(c.address_zip_code, '`', '''') AS address_zip_code,
    replace(c.address_city, '`', '''') AS address_city,
    UPPER(country.code) AS address_country,

    -- Socials
    replace(c.social_facebook, '`', '''') AS social_facebook,
    replace(c.social_twitter, '`', '''') AS social_twitter,
    replace(c.social_linked_in, '`', '''') AS social_linked_in,
    replace(c.social_telegram, '`', '''') AS social_telegram,
    replace(c.social_whatsapp, '`', '''') AS social_whatsapp,

    -- Picture
    p.pathname AS picture,
    md5(c.email) AS email_hash,

    -- Metadata
    c.settings_receive_newsletters, c.settings_receive_sms, c.settings_receive_calls, c.created_at,
    replace(to_char(c.created_at, 'YYYY-MM-DD'), '-', '')::int as created_at_int,

    -- Status
    (CASE
         WHEN c.account_password IS NOT NULL THEN 'm'
         WHEN NOT settings_receive_newsletters THEN 'u'
         ELSE 'c'
        END) AS status,

    -- Areas
    (
        WITH RECURSIVE tree (id) as
                           (
                               SELECT a.id, a.code, a.name, a.type, a.parent_id from areas a where a.id = c.area_id
                               UNION ALL
                               SELECT a.id, a.code, a.name, a.type, a.parent_id from tree, areas a where a.id = tree.parent_id
                           )
        SELECT string_agg(concat(id, '×', code, '×', name, '×', type), '✂')
        FROM tree
    ) AS area,

    -- Tags
    (
        SELECT string_agg(concat(t.id, '×', t.name), '✂')
        FROM community_contacts_tags ct
                 LEFT JOIN community_tags t ON ct.tag_id = t.id
        WHERE ct.contact_id = c.id
    ) AS tags,

    -- Projects
    (
        SELECT string_agg(concat(uuid, '×', name), '✂')
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
        SELECT string_agg(eo.uuid::text, '✂')
        FROM community_emailing_campaigns_messages ceo
                 LEFT JOIN community_emailing_campaigns eo ON eo.id = ceo.campaign_id
        WHERE ceo.contact_id = c.id
          AND ceo.opened = true
    ) as opened_emails,

    -- Clicked emails
    (
        SELECT string_agg(ec.uuid::text, '✂')
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
EOT);
    }

    public function down(Schema $schema): void
    {
        // Drop the updated view
        $this->addSql('DROP VIEW indexing_view');

        // Restore the previous version of the view
        $this->addSql(<<<EOT
CREATE VIEW indexing_view AS
SELECT
    -- Organization
    o.uuid AS organization,

    -- Details
    c.uuid,
    replace(c.email, '`', '''') AS email,
    (
        SELECT replace(replace(string_agg(value::text, '✂'), '"', ''), '`', '''')
        FROM json_array_elements(c.contact_additional_emails)
    ) AS contact_additional_emails,
    replace(c.contact_phone, '`', '''') AS contact_phone,
    c.parsed_contact_phone,
    replace(c.contact_work_phone, '`', '''') AS contact_work_phone,
    c.parsed_contact_work_phone,

    -- Profile
    replace(c.profile_formal_title, '`', '''') AS profile_formal_title,
    replace(c.profile_first_name, '`', '''') AS profile_first_name,
    c.profile_first_name_slug,
    replace(c.profile_middle_name, '`', '''') AS profile_middle_name,
    c.profile_middle_name_slug,
    replace(c.profile_last_name, '`', '''') AS profile_last_name,
    c.profile_last_name_slug,
    c.profile_birthdate,
    replace(profile_birthdate::text, '-', '')::int as profile_birthdate_int,
    extract(year from age(now(), profile_birthdate))::int as profile_age,
    c.profile_gender,
    c.profile_nationality,
    replace(c.profile_company, '`', '''') AS profile_company,
    c.profile_company_slug,
    replace(c.profile_job_title, '`', '''') AS profile_job_title,
    c.profile_job_title_slug,

    -- Address
    replace(c.address_street_line1, '`', '''') AS address_street_line1,
    c.address_street_line1_slug,
    replace(c.address_street_line2, '`', '''') AS address_street_line2,
    c.address_street_line2_slug,
    replace(c.address_zip_code, '`', '''') AS address_zip_code,
    replace(c.address_city, '`', '''') AS address_city,
    UPPER(country.code) AS address_country,

    -- Socials
    replace(c.social_facebook, '`', '''') AS social_facebook,
    replace(c.social_twitter, '`', '''') AS social_twitter,
    replace(c.social_linked_in, '`', '''') AS social_linked_in,
    replace(c.social_telegram, '`', '''') AS social_telegram,
    replace(c.social_whatsapp, '`', '''') AS social_whatsapp,

    -- Picture
    p.pathname AS picture,
    md5(c.email) AS email_hash,

    -- Metadata
    c.settings_receive_newsletters, c.settings_receive_sms, c.settings_receive_calls, c.created_at,
    replace(to_char(c.created_at, 'YYYY-MM-DD'), '-', '')::int as created_at_int,

    -- Status
    (CASE
         WHEN c.account_password IS NOT NULL THEN 'm'
         WHEN NOT settings_receive_newsletters AND NOT settings_receive_calls AND NOT settings_receive_sms THEN 'u'
         ELSE 'c'
        END) AS status,

    -- Areas
    (
        WITH RECURSIVE tree (id) as
                           (
                               SELECT a.id, a.code, a.name, a.type, a.parent_id from areas a where a.id = c.area_id
                               UNION ALL
                               SELECT a.id, a.code, a.name, a.type, a.parent_id from tree, areas a where a.id = tree.parent_id
                           )
        SELECT string_agg(concat(id, '×', code, '×', name, '×', type), '✂')
        FROM tree
    ) AS area,

    -- Tags
    (
        SELECT string_agg(concat(t.id, '×', t.name), '✂')
        FROM community_contacts_tags ct
                 LEFT JOIN community_tags t ON ct.tag_id = t.id
        WHERE ct.contact_id = c.id
    ) AS tags,

    -- Projects
    (
        SELECT string_agg(concat(uuid, '×', name), '✂')
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
        SELECT string_agg(eo.uuid::text, '✂')
        FROM community_emailing_campaigns_messages ceo
                 LEFT JOIN community_emailing_campaigns eo ON eo.id = ceo.campaign_id
        WHERE ceo.contact_id = c.id
          AND ceo.opened = true
    ) as opened_emails,

    -- Clicked emails
    (
        SELECT string_agg(ec.uuid::text, '✂')
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
EOT);
    }
}
