<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200921212257 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE community_email_automations_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE community_email_automations_messages_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE community_email_automations (id BIGINT NOT NULL, area_filter_id BIGINT DEFAULT NULL, tag_filter_id BIGINT DEFAULT NULL, organization_id BIGINT NOT NULL, name VARCHAR(100) NOT NULL, trigger VARCHAR(50) NOT NULL, to_email VARCHAR(250) DEFAULT NULL, from_email VARCHAR(250) NOT NULL, from_name VARCHAR(150) DEFAULT NULL, subject VARCHAR(150) NOT NULL, preview VARCHAR(150) DEFAULT NULL, content TEXT DEFAULT NULL, type_filter VARCHAR(20) DEFAULT NULL, weight INT NOT NULL, enabled BOOLEAN NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E1E25137D17F50A6 ON community_email_automations (uuid)');
        $this->addSql('CREATE INDEX IDX_E1E2513746E9F782 ON community_email_automations (area_filter_id)');
        $this->addSql('CREATE INDEX IDX_E1E25137C2A28879 ON community_email_automations (tag_filter_id)');
        $this->addSql('CREATE INDEX IDX_E1E2513732C8A3DE ON community_email_automations (organization_id)');
        $this->addSql('COMMENT ON COLUMN community_email_automations.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE community_email_automations_messages (id BIGINT NOT NULL, automation_id BIGINT NOT NULL, email VARCHAR(250) NOT NULL, formal_title VARCHAR(20) DEFAULT NULL, first_name VARCHAR(150) DEFAULT NULL, middle_name VARCHAR(150) DEFAULT NULL, last_name VARCHAR(150) DEFAULT NULL, sent_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6F397AACD1C5DDC3 ON community_email_automations_messages (automation_id)');
        $this->addSql('ALTER TABLE community_email_automations ADD CONSTRAINT FK_E1E2513746E9F782 FOREIGN KEY (area_filter_id) REFERENCES areas (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_email_automations ADD CONSTRAINT FK_E1E25137C2A28879 FOREIGN KEY (tag_filter_id) REFERENCES community_tags (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_email_automations ADD CONSTRAINT FK_E1E2513732C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_email_automations_messages ADD CONSTRAINT FK_6F397AACD1C5DDC3 FOREIGN KEY (automation_id) REFERENCES community_email_automations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscriptions_logs DROP CONSTRAINT FK_27FC727932C8A3DE');
        $this->addSql('ALTER TABLE subscriptions_logs ADD CONSTRAINT FK_27FC727932C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE projects DROP CONSTRAINT FK_5C93B3A432C8A3DE');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A432C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE organizations_members DROP CONSTRAINT FK_BA35809732C8A3DE');
        $this->addSql('ALTER TABLE organizations_members ADD CONSTRAINT FK_BA35809732C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE domains DROP CONSTRAINT FK_8C7BBF9D32C8A3DE');
        $this->addSql('ALTER TABLE domains ADD CONSTRAINT FK_8C7BBF9D32C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_tags DROP CONSTRAINT FK_AEED7EEC32C8A3DE');
        $this->addSql('ALTER TABLE community_tags ADD CONSTRAINT FK_AEED7EEC32C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_contacts DROP CONSTRAINT FK_C106D05432C8A3DE');
        $this->addSql('ALTER TABLE community_contacts ADD CONSTRAINT FK_C106D05432C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_email_automations_messages DROP CONSTRAINT FK_6F397AACD1C5DDC3');
        $this->addSql('DROP SEQUENCE community_email_automations_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE community_email_automations_messages_id_seq CASCADE');
        $this->addSql('DROP TABLE community_email_automations');
        $this->addSql('DROP TABLE community_email_automations_messages');
        $this->addSql('ALTER TABLE community_contacts DROP CONSTRAINT fk_c106d05432c8a3de');
        $this->addSql('ALTER TABLE community_contacts ADD CONSTRAINT fk_c106d05432c8a3de FOREIGN KEY (organization_id) REFERENCES organizations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscriptions_logs DROP CONSTRAINT fk_27fc727932c8a3de');
        $this->addSql('ALTER TABLE subscriptions_logs ADD CONSTRAINT fk_27fc727932c8a3de FOREIGN KEY (organization_id) REFERENCES organizations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE domains DROP CONSTRAINT fk_8c7bbf9d32c8a3de');
        $this->addSql('ALTER TABLE domains ADD CONSTRAINT fk_8c7bbf9d32c8a3de FOREIGN KEY (organization_id) REFERENCES organizations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE organizations_members DROP CONSTRAINT fk_ba35809732c8a3de');
        $this->addSql('ALTER TABLE organizations_members ADD CONSTRAINT fk_ba35809732c8a3de FOREIGN KEY (organization_id) REFERENCES organizations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_tags DROP CONSTRAINT fk_aeed7eec32c8a3de');
        $this->addSql('ALTER TABLE community_tags ADD CONSTRAINT fk_aeed7eec32c8a3de FOREIGN KEY (organization_id) REFERENCES organizations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE projects DROP CONSTRAINT fk_5c93b3a432c8a3de');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT fk_5c93b3a432c8a3de FOREIGN KEY (organization_id) REFERENCES organizations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
