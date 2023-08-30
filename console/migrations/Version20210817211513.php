<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210817211513 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE community_texting_campaigns_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE community_texting_campaigns_messages_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE community_texting_campaigns (id BIGINT NOT NULL, project_id BIGINT NOT NULL, content VARCHAR(160) NOT NULL, tags_filter_type VARCHAR(10) NOT NULL, contacts_filter JSON DEFAULT NULL, sent_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, resolved_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, only_for_members BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_70856D8DD17F50A6 ON community_texting_campaigns (uuid)');
        $this->addSql('CREATE INDEX IDX_70856D8D166D1F9C ON community_texting_campaigns (project_id)');
        $this->addSql('COMMENT ON COLUMN community_texting_campaigns.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE community_texting_campaigns_areas_filter (texting_campaign_id BIGINT NOT NULL, area_id BIGINT NOT NULL, PRIMARY KEY(texting_campaign_id, area_id))');
        $this->addSql('CREATE INDEX IDX_D0E1F01912B765B9 ON community_texting_campaigns_areas_filter (texting_campaign_id)');
        $this->addSql('CREATE INDEX IDX_D0E1F019BD0F409C ON community_texting_campaigns_areas_filter (area_id)');
        $this->addSql('CREATE TABLE community_texting_campaigns_tags_filter (texting_campaign_id BIGINT NOT NULL, tag_id BIGINT NOT NULL, PRIMARY KEY(texting_campaign_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_D105E47212B765B9 ON community_texting_campaigns_tags_filter (texting_campaign_id)');
        $this->addSql('CREATE INDEX IDX_D105E472BAD26311 ON community_texting_campaigns_tags_filter (tag_id)');
        $this->addSql('CREATE TABLE community_texting_campaigns_messages (id BIGINT NOT NULL, campaign_id BIGINT NOT NULL, contact_id BIGINT NOT NULL, sent BOOLEAN NOT NULL, sent_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, delivered BOOLEAN NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, bounced BOOLEAN NOT NULL, bounced_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F83AC110F639F774 ON community_texting_campaigns_messages (campaign_id)');
        $this->addSql('CREATE INDEX IDX_F83AC110E7A1254A ON community_texting_campaigns_messages (contact_id)');
        $this->addSql('ALTER TABLE community_texting_campaigns ADD CONSTRAINT FK_70856D8D166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_texting_campaigns_areas_filter ADD CONSTRAINT FK_D0E1F01912B765B9 FOREIGN KEY (texting_campaign_id) REFERENCES community_texting_campaigns (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_texting_campaigns_areas_filter ADD CONSTRAINT FK_D0E1F019BD0F409C FOREIGN KEY (area_id) REFERENCES areas (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_texting_campaigns_tags_filter ADD CONSTRAINT FK_D105E47212B765B9 FOREIGN KEY (texting_campaign_id) REFERENCES community_texting_campaigns (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_texting_campaigns_tags_filter ADD CONSTRAINT FK_D105E472BAD26311 FOREIGN KEY (tag_id) REFERENCES community_tags (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_texting_campaigns_messages ADD CONSTRAINT FK_F83AC110F639F774 FOREIGN KEY (campaign_id) REFERENCES community_texting_campaigns (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_texting_campaigns_messages ADD CONSTRAINT FK_F83AC110E7A1254A FOREIGN KEY (contact_id) REFERENCES community_contacts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_texting_campaigns_areas_filter DROP CONSTRAINT FK_D0E1F01912B765B9');
        $this->addSql('ALTER TABLE community_texting_campaigns_tags_filter DROP CONSTRAINT FK_D105E47212B765B9');
        $this->addSql('ALTER TABLE community_texting_campaigns_messages DROP CONSTRAINT FK_F83AC110F639F774');
        $this->addSql('DROP SEQUENCE community_texting_campaigns_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE community_texting_campaigns_messages_id_seq CASCADE');
        $this->addSql('DROP TABLE community_texting_campaigns');
        $this->addSql('DROP TABLE community_texting_campaigns_areas_filter');
        $this->addSql('DROP TABLE community_texting_campaigns_tags_filter');
        $this->addSql('DROP TABLE community_texting_campaigns_messages');
    }
}
