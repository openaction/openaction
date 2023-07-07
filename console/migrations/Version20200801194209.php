<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200801194209 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects ADD website_sharer_id BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE projects ADD website_meta_title VARCHAR(60) DEFAULT NULL');
        $this->addSql('ALTER TABLE projects ADD website_meta_description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A4DD3EDFF7 FOREIGN KEY (website_sharer_id) REFERENCES uploads (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5C93B3A4DD3EDFF7 ON projects (website_sharer_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects DROP CONSTRAINT FK_5C93B3A4DD3EDFF7');
        $this->addSql('DROP INDEX UNIQ_5C93B3A4DD3EDFF7');
        $this->addSql('ALTER TABLE projects DROP website_sharer_id');
        $this->addSql('ALTER TABLE projects DROP website_meta_title');
        $this->addSql('ALTER TABLE projects DROP website_meta_description');
    }
}
