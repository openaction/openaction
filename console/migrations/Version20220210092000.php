<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220210092000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects ADD website_main_video_id BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A4DEC33304 FOREIGN KEY (website_main_video_id) REFERENCES uploads (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5C93B3A4DEC33304 ON projects (website_main_video_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects DROP CONSTRAINT FK_5C93B3A4DEC33304');
        $this->addSql('DROP INDEX UNIQ_5C93B3A4DEC33304');
        $this->addSql('ALTER TABLE projects DROP website_main_video_id');
    }
}
