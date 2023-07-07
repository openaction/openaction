<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210723212015 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE themes_assets_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE themes_assets (id BIGINT NOT NULL, file_id BIGINT DEFAULT NULL, project_id BIGINT NOT NULL, name VARCHAR(100) NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7758E837D17F50A6 ON themes_assets (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7758E83793CB796C ON themes_assets (file_id)');
        $this->addSql('CREATE INDEX IDX_7758E837166D1F9C ON themes_assets (project_id)');
        $this->addSql('COMMENT ON COLUMN themes_assets.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE themes_assets ADD CONSTRAINT FK_7758E83793CB796C FOREIGN KEY (file_id) REFERENCES uploads (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE themes_assets ADD CONSTRAINT FK_7758E837166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE themes_assets_id_seq CASCADE');
        $this->addSql('DROP TABLE themes_assets');
    }
}
