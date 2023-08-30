<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200913101519 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // Create page blocks
        $this->addSql('CREATE SEQUENCE website_page_blocks_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE website_page_blocks (id BIGINT NOT NULL, project_id BIGINT NOT NULL, page VARCHAR(30) NOT NULL, type VARCHAR(30) NOT NULL, weight INT NOT NULL, config JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EABE9AEF166D1F9C ON website_page_blocks (project_id)');
        $this->addSql('ALTER TABLE website_page_blocks ADD CONSTRAINT FK_EABE9AEF166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Migrate home blocks to page blocks
        $this->addSql('
            INSERT INTO website_page_blocks (id, project_id, page, type, weight, config, created_at, updated_at)
                SELECT nextval(\'website_page_blocks_id_seq\'), project_id, \'home\', type, weight, data, created_at, updated_at
                FROM website_home_blocks
        ');

        // Remove home blocks
        $this->addSql('DROP SEQUENCE website_home_blocks_id_seq CASCADE');
        $this->addSql('DROP TABLE website_home_blocks');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE website_page_blocks_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE website_home_blocks_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE website_home_blocks (id BIGINT NOT NULL, project_id BIGINT NOT NULL, type VARCHAR(20) NOT NULL, data JSON NOT NULL, weight INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_756c054c166d1f9c ON website_home_blocks (project_id)');
        $this->addSql('ALTER TABLE website_home_blocks ADD CONSTRAINT fk_756c054c166d1f9c FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE website_page_blocks');
    }
}
