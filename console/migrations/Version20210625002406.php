<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210625002406 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE website_redirections_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE website_redirections (id BIGINT NOT NULL, project_id BIGINT NOT NULL, source VARCHAR(250) NOT NULL, target VARCHAR(250) NOT NULL, code INT NOT NULL, weight INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_ACDA5299166D1F9C ON website_redirections (project_id)');
        $this->addSql('ALTER TABLE website_redirections ADD CONSTRAINT FK_ACDA5299166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE website_redirections_id_seq CASCADE');
        $this->addSql('DROP TABLE website_redirections');
    }
}
