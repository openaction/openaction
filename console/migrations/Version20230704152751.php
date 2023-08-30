<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230704152751 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_contacts_logs DROP CONSTRAINT FK_9001FC68E7A1254A');
        $this->addSql('ALTER TABLE community_contacts_logs ADD CONSTRAINT FK_9001FC68E7A1254A FOREIGN KEY (contact_id) REFERENCES community_contacts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_contacts_updates DROP CONSTRAINT FK_49832E42E7A1254A');
        $this->addSql('ALTER TABLE community_contacts_updates ADD CONSTRAINT FK_49832E42E7A1254A FOREIGN KEY (contact_id) REFERENCES community_contacts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_emailing_campaigns_messages_logs DROP CONSTRAINT FK_1651F3D9537A1329');
        $this->addSql('ALTER TABLE community_emailing_campaigns_messages_logs ADD CONSTRAINT FK_1651F3D9537A1329 FOREIGN KEY (message_id) REFERENCES community_emailing_campaigns_messages (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_phoning_campaigns DROP CONSTRAINT FK_61217EB95FF69B7D');
        $this->addSql('ALTER TABLE community_phoning_campaigns ADD CONSTRAINT FK_61217EB95FF69B7D FOREIGN KEY (form_id) REFERENCES website_forms (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE integrations_telegram_apps_authorizations DROP CONSTRAINT FK_3CEAEE847987212D');
        $this->addSql('ALTER TABLE integrations_telegram_apps_authorizations DROP CONSTRAINT FK_3CEAEE847597D3FE');
        $this->addSql('ALTER TABLE integrations_telegram_apps_authorizations ADD CONSTRAINT FK_3CEAEE847987212D FOREIGN KEY (app_id) REFERENCES integrations_telegram_apps (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE integrations_telegram_apps_authorizations ADD CONSTRAINT FK_3CEAEE847597D3FE FOREIGN KEY (member_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE organizations_main_tags DROP CONSTRAINT FK_A74E88A6BAD26311');
        $this->addSql('ALTER TABLE organizations_main_tags ADD CONSTRAINT FK_A74E88A6BAD26311 FOREIGN KEY (tag_id) REFERENCES community_tags (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE organizations_members DROP CONSTRAINT FK_BA3580977597D3FE');
        $this->addSql('ALTER TABLE organizations_members ADD CONSTRAINT FK_BA3580977597D3FE FOREIGN KEY (member_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE registrations DROP CONSTRAINT FK_53DE51E732C8A3DE');
        $this->addSql('ALTER TABLE registrations ADD CONSTRAINT FK_53DE51E732C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users_visits DROP CONSTRAINT FK_4BAFB77A7E3C61F9');
        $this->addSql('ALTER TABLE users_visits ADD CONSTRAINT FK_4BAFB77A7E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE website_themes DROP CONSTRAINT FK_87C7D6F5F675F31B');
        $this->addSql('ALTER TABLE website_themes ADD CONSTRAINT FK_87C7D6F5F675F31B FOREIGN KEY (author_id) REFERENCES users (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE indexing_crm (organization UUID DEFAULT NULL, uuid UUID NOT NULL, email VARCHAR(250) DEFAULT NULL, contact_additional_emails TEXT DEFAULT NULL, contact_phone VARCHAR(50) DEFAULT NULL, profile_formal_title VARCHAR(20) DEFAULT NULL, profile_first_name VARCHAR(150) DEFAULT NULL, profile_middle_name VARCHAR(150) DEFAULT NULL, profile_last_name VARCHAR(150) DEFAULT NULL, profile_birthdate DATE DEFAULT NULL, profile_birthdate_int INT DEFAULT NULL, profile_age INT DEFAULT NULL, profile_gender VARCHAR(20) DEFAULT NULL, profile_nationality VARCHAR(2) DEFAULT NULL, profile_company VARCHAR(150) DEFAULT NULL, profile_job_title VARCHAR(150) DEFAULT NULL, address_street_line1 VARCHAR(150) DEFAULT NULL, address_street_line2 VARCHAR(150) DEFAULT NULL, address_zip_code VARCHAR(150) DEFAULT NULL, address_city VARCHAR(150) DEFAULT NULL, address_country VARCHAR(2) DEFAULT NULL, social_facebook VARCHAR(150) DEFAULT NULL, social_twitter VARCHAR(150) DEFAULT NULL, social_linked_in VARCHAR(150) DEFAULT NULL, social_telegram VARCHAR(150) DEFAULT NULL, social_whatsapp VARCHAR(150) DEFAULT NULL, picture VARCHAR(250) DEFAULT NULL, email_hash VARCHAR(32) DEFAULT NULL, settings_receive_newsletters BOOLEAN DEFAULT NULL, settings_receive_sms BOOLEAN DEFAULT NULL, settings_receive_calls BOOLEAN DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, status VARCHAR(1) DEFAULT NULL, area TEXT DEFAULT NULL, tags TEXT DEFAULT NULL, projects TEXT DEFAULT NULL, opened_emails TEXT DEFAULT NULL, clicked_emails TEXT DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX indexing_crm_uuid_key ON indexing_crm (uuid)');
        $this->addSql('ALTER TABLE community_contacts_logs DROP CONSTRAINT fk_9001fc68e7a1254a');
        $this->addSql('ALTER TABLE community_contacts_logs ADD CONSTRAINT fk_9001fc68e7a1254a FOREIGN KEY (contact_id) REFERENCES community_contacts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE organizations_main_tags DROP CONSTRAINT fk_a74e88a6bad26311');
        $this->addSql('ALTER TABLE organizations_main_tags ADD CONSTRAINT fk_a74e88a6bad26311 FOREIGN KEY (tag_id) REFERENCES community_tags (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_contacts_updates DROP CONSTRAINT fk_49832e42e7a1254a');
        $this->addSql('ALTER TABLE community_contacts_updates ADD CONSTRAINT fk_49832e42e7a1254a FOREIGN KEY (contact_id) REFERENCES community_contacts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE uploads ADD last_accessed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE website_themes DROP CONSTRAINT fk_87c7d6f5f675f31b');
        $this->addSql('ALTER TABLE website_themes ADD CONSTRAINT fk_87c7d6f5f675f31b FOREIGN KEY (author_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_phoning_campaigns DROP CONSTRAINT fk_61217eb95ff69b7d');
        $this->addSql('ALTER TABLE community_phoning_campaigns ADD CONSTRAINT fk_61217eb95ff69b7d FOREIGN KEY (form_id) REFERENCES website_forms (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP INDEX projects_tags_pkey');
        $this->addSql('ALTER TABLE projects_tags ADD PRIMARY KEY (project_id, tag_id)');
        $this->addSql('ALTER TABLE registrations DROP CONSTRAINT fk_53de51e732c8a3de');
        $this->addSql('ALTER TABLE registrations ADD CONSTRAINT fk_53de51e732c8a3de FOREIGN KEY (organization_id) REFERENCES organizations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE integrations_telegram_apps_authorizations DROP CONSTRAINT fk_3ceaee847987212d');
        $this->addSql('ALTER TABLE integrations_telegram_apps_authorizations DROP CONSTRAINT fk_3ceaee847597d3fe');
        $this->addSql('ALTER TABLE integrations_telegram_apps_authorizations ADD CONSTRAINT fk_3ceaee847987212d FOREIGN KEY (app_id) REFERENCES integrations_telegram_apps (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE integrations_telegram_apps_authorizations ADD CONSTRAINT fk_3ceaee847597d3fe FOREIGN KEY (member_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_emailing_campaigns_messages_logs DROP CONSTRAINT fk_1651f3d9537a1329');
        $this->addSql('ALTER TABLE community_emailing_campaigns_messages_logs ADD CONSTRAINT fk_1651f3d9537a1329 FOREIGN KEY (message_id) REFERENCES community_emailing_campaigns_messages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_imports ALTER job_id DROP NOT NULL');
        $this->addSql('ALTER TABLE community_contacts ALTER settings_by_project TYPE JSONB');
        $this->addSql('CREATE INDEX community_contacts_settings_by_projects_idx ON community_contacts (settings_by_project)');
        $this->addSql('ALTER TABLE users_visits DROP CONSTRAINT fk_4bafb77a7e3c61f9');
        $this->addSql('ALTER TABLE users_visits ADD CONSTRAINT fk_4bafb77a7e3c61f9 FOREIGN KEY (owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE organizations_members DROP CONSTRAINT fk_ba3580977597d3fe');
        $this->addSql('ALTER TABLE organizations_members ADD CONSTRAINT fk_ba3580977597d3fe FOREIGN KEY (member_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
