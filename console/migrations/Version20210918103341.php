<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210918103341 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE integrations_integromat_webhooks_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE integrations_integromat_webhooks (id BIGINT NOT NULL, organization_id BIGINT NOT NULL, token VARCHAR(64) NOT NULL, integromat_url VARCHAR(64) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D39E9FC65F37A13B ON integrations_integromat_webhooks (token)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D39E9FC6588D2D61 ON integrations_integromat_webhooks (integromat_url)');
        $this->addSql('CREATE INDEX IDX_D39E9FC632C8A3DE ON integrations_integromat_webhooks (organization_id)');
        $this->addSql('ALTER TABLE integrations_integromat_webhooks ADD CONSTRAINT FK_D39E9FC632C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE organizations ADD api_token VARCHAR(64) DEFAULT NULL');
        $this->addSql('UPDATE organizations SET api_token = CONCAT(MD5(RANDOM()::text), MD5(RANDOM()::text))');
        $this->addSql('ALTER TABLE organizations ALTER api_token DROP DEFAULT');
        $this->addSql('ALTER TABLE organizations ALTER api_token SET NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_427C1C7F7BA2F5EB ON organizations (api_token)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE integrations_integromat_webhooks_id_seq CASCADE');
        $this->addSql('DROP TABLE integrations_integromat_webhooks');
        $this->addSql('DROP INDEX UNIQ_427C1C7F7BA2F5EB');
        $this->addSql('ALTER TABLE organizations DROP api_token');
    }
}
