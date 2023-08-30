<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220803135809 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE platform_jobs (id BIGSERIAL NOT NULL, type VARCHAR(50) NOT NULL, step BIGINT NOT NULL, total BIGINT NOT NULL, payload JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE organizations ADD crm_search_key_uid UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN organizations.crm_search_key_uid IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE platform_jobs');
        $this->addSql('ALTER TABLE organizations DROP crm_search_key_uid');
    }
}
