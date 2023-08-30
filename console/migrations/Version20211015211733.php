<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211015211733 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE community_phoning_campaigns_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE community_phoning_campaigns_calls_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE community_phoning_campaigns_targets_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE community_phoning_campaigns (id BIGINT NOT NULL, form_id BIGINT NOT NULL, project_id BIGINT NOT NULL, name VARCHAR(160) NOT NULL, tags_filter_type VARCHAR(10) NOT NULL, contacts_filter JSON DEFAULT NULL, start_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, end_after INT DEFAULT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, only_for_members BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_61217EB9D17F50A6 ON community_phoning_campaigns (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_61217EB95FF69B7D ON community_phoning_campaigns (form_id)');
        $this->addSql('CREATE INDEX IDX_61217EB9166D1F9C ON community_phoning_campaigns (project_id)');
        $this->addSql('COMMENT ON COLUMN community_phoning_campaigns.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE community_phoning_campaigns_areas_filter (phoning_campaign_id BIGINT NOT NULL, area_id BIGINT NOT NULL, PRIMARY KEY(phoning_campaign_id, area_id))');
        $this->addSql('CREATE INDEX IDX_E56F6BB02D23C634 ON community_phoning_campaigns_areas_filter (phoning_campaign_id)');
        $this->addSql('CREATE INDEX IDX_E56F6BB0BD0F409C ON community_phoning_campaigns_areas_filter (area_id)');
        $this->addSql('CREATE TABLE community_phoning_campaigns_tags_filter (phoning_campaign_id BIGINT NOT NULL, tag_id BIGINT NOT NULL, PRIMARY KEY(phoning_campaign_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_EA36B75A2D23C634 ON community_phoning_campaigns_tags_filter (phoning_campaign_id)');
        $this->addSql('CREATE INDEX IDX_EA36B75ABAD26311 ON community_phoning_campaigns_tags_filter (tag_id)');
        $this->addSql('CREATE TABLE community_phoning_campaigns_calls (id BIGINT NOT NULL, target_id BIGINT NOT NULL, author_id BIGINT NOT NULL, status VARCHAR(15) NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7A27C54CD17F50A6 ON community_phoning_campaigns_calls (uuid)');
        $this->addSql('CREATE INDEX IDX_7A27C54C158E0B66 ON community_phoning_campaigns_calls (target_id)');
        $this->addSql('CREATE INDEX IDX_7A27C54CF675F31B ON community_phoning_campaigns_calls (author_id)');
        $this->addSql('COMMENT ON COLUMN community_phoning_campaigns_calls.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE community_phoning_campaigns_targets (id BIGINT NOT NULL, campaign_id BIGINT NOT NULL, contact_id BIGINT NOT NULL, answer_id BIGINT DEFAULT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C64711FAD17F50A6 ON community_phoning_campaigns_targets (uuid)');
        $this->addSql('CREATE INDEX IDX_C64711FAF639F774 ON community_phoning_campaigns_targets (campaign_id)');
        $this->addSql('CREATE INDEX IDX_C64711FAE7A1254A ON community_phoning_campaigns_targets (contact_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C64711FAAA334807 ON community_phoning_campaigns_targets (answer_id)');
        $this->addSql('COMMENT ON COLUMN community_phoning_campaigns_targets.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE community_phoning_campaigns ADD CONSTRAINT FK_61217EB95FF69B7D FOREIGN KEY (form_id) REFERENCES website_forms (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_phoning_campaigns ADD CONSTRAINT FK_61217EB9166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_phoning_campaigns_areas_filter ADD CONSTRAINT FK_E56F6BB02D23C634 FOREIGN KEY (phoning_campaign_id) REFERENCES community_phoning_campaigns (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_phoning_campaigns_areas_filter ADD CONSTRAINT FK_E56F6BB0BD0F409C FOREIGN KEY (area_id) REFERENCES areas (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_phoning_campaigns_tags_filter ADD CONSTRAINT FK_EA36B75A2D23C634 FOREIGN KEY (phoning_campaign_id) REFERENCES community_phoning_campaigns (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_phoning_campaigns_tags_filter ADD CONSTRAINT FK_EA36B75ABAD26311 FOREIGN KEY (tag_id) REFERENCES community_tags (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_phoning_campaigns_calls ADD CONSTRAINT FK_7A27C54C158E0B66 FOREIGN KEY (target_id) REFERENCES community_phoning_campaigns_targets (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_phoning_campaigns_calls ADD CONSTRAINT FK_7A27C54CF675F31B FOREIGN KEY (author_id) REFERENCES community_contacts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_phoning_campaigns_targets ADD CONSTRAINT FK_C64711FAF639F774 FOREIGN KEY (campaign_id) REFERENCES community_phoning_campaigns (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_phoning_campaigns_targets ADD CONSTRAINT FK_C64711FAE7A1254A FOREIGN KEY (contact_id) REFERENCES community_contacts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_phoning_campaigns_targets ADD CONSTRAINT FK_C64711FAAA334807 FOREIGN KEY (answer_id) REFERENCES website_forms_answers (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_phoning_campaigns_areas_filter DROP CONSTRAINT FK_E56F6BB02D23C634');
        $this->addSql('ALTER TABLE community_phoning_campaigns_tags_filter DROP CONSTRAINT FK_EA36B75A2D23C634');
        $this->addSql('ALTER TABLE community_phoning_campaigns_targets DROP CONSTRAINT FK_C64711FAF639F774');
        $this->addSql('ALTER TABLE community_phoning_campaigns_calls DROP CONSTRAINT FK_7A27C54C158E0B66');
        $this->addSql('DROP SEQUENCE community_phoning_campaigns_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE community_phoning_campaigns_calls_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE community_phoning_campaigns_targets_id_seq CASCADE');
        $this->addSql('DROP TABLE community_phoning_campaigns');
        $this->addSql('DROP TABLE community_phoning_campaigns_areas_filter');
        $this->addSql('DROP TABLE community_phoning_campaigns_tags_filter');
        $this->addSql('DROP TABLE community_phoning_campaigns_calls');
        $this->addSql('DROP TABLE community_phoning_campaigns_targets');
    }
}
