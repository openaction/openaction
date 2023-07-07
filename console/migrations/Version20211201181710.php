<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211201181710 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects ADD website_theme_id BIGINT DEFAULT NULL');
        $this->addSql('
            UPDATE projects
            SET website_theme_id = t.id
            FROM (SELECT id FROM website_themes WHERE repository_full_name = \'citipo/theme-bold\') AS t
            WHERE website_theme = \'classic\'
        ');
        $this->addSql('
            UPDATE projects
            SET website_theme_id = t.id
            FROM (SELECT id FROM website_themes WHERE repository_full_name = \'citipo/theme-structured\') AS t
            WHERE website_theme = \'campaign\'
        ');
        $this->addSql('
            UPDATE projects
            SET website_theme_id = t.id
            FROM (SELECT id FROM website_themes WHERE repository_full_name = \'citipo/theme-efficient\') AS t
            WHERE website_theme = \'narrow\'
        ');
        $this->addSql('ALTER TABLE projects ALTER COLUMN website_theme DROP NOT NULL');
        $this->addSql('ALTER TABLE projects ALTER COLUMN website_theme SET DEFAULT NULL');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A488153D0A FOREIGN KEY (website_theme_id) REFERENCES website_themes (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5C93B3A488153D0A ON projects (website_theme_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects DROP CONSTRAINT FK_5C93B3A488153D0A');
        $this->addSql('DROP INDEX IDX_5C93B3A488153D0A');
        $this->addSql('ALTER TABLE projects DROP website_theme_id');
    }
}
