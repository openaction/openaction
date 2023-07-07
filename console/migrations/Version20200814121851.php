<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200814121851 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE website_home_blocks_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE website_home_blocks (id BIGINT NOT NULL, project_id BIGINT NOT NULL, type VARCHAR(20) NOT NULL, data JSON NOT NULL, weight INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_756C054C166D1F9C ON website_home_blocks (project_id)');
        $this->addSql('ALTER TABLE website_home_blocks ADD CONSTRAINT FK_756C054C166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE projects ADD website_main_image_id BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE projects ADD website_main_intro_title VARCHAR(80) DEFAULT NULL');
        $this->addSql('ALTER TABLE projects ADD website_main_intro_content TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A4CAA71627 FOREIGN KEY (website_main_image_id) REFERENCES uploads (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5C93B3A4CAA71627 ON projects (website_main_image_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE website_home_blocks_id_seq CASCADE');
        $this->addSql('DROP TABLE website_home_blocks');
        $this->addSql('ALTER TABLE projects DROP CONSTRAINT FK_5C93B3A4CAA71627');
        $this->addSql('DROP INDEX UNIQ_5C93B3A4CAA71627');
        $this->addSql('ALTER TABLE projects DROP website_main_image_id');
        $this->addSql('ALTER TABLE projects DROP website_main_intro_title');
        $this->addSql('ALTER TABLE projects DROP website_main_intro_content');
    }
}
