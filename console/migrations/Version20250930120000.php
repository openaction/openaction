<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250930120000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS integrations_revue_accounts');
        $this->addSql('DROP SEQUENCE IF EXISTS integrations_revue_accounts_id_seq');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE integrations_revue_accounts_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE integrations_revue_accounts (id BIGINT NOT NULL, organization_id BIGINT NOT NULL, label VARCHAR(50) NOT NULL, api_token VARCHAR(80) NOT NULL, enabled BOOLEAN NOT NULL, last_sync TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B6FC619D17F50A6 ON integrations_revue_accounts (uuid)');
        $this->addSql('CREATE INDEX IDX_B6FC61932C8A3DE ON integrations_revue_accounts (organization_id)');
        $this->addSql('COMMENT ON COLUMN integrations_revue_accounts.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE integrations_revue_accounts ADD CONSTRAINT FK_B6FC61932C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE integrations_revue_accounts ALTER id SET DEFAULT nextval(\'integrations_revue_accounts_id_seq\')');
    }
}
