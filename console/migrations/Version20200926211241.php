<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200926211241 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects ADD website_font_title VARCHAR(100) DEFAULT \'Merriweather Sans\'');
        $this->addSql('ALTER TABLE projects ALTER website_font_title DROP DEFAULT');
        $this->addSql('ALTER TABLE projects ALTER website_font_title SET NOT NULL');
        $this->addSql('ALTER TABLE projects ADD website_font_text VARCHAR(100) DEFAULT \'Merriweather\'');
        $this->addSql('ALTER TABLE projects ALTER website_font_text DROP DEFAULT');
        $this->addSql('ALTER TABLE projects ALTER website_font_text SET NOT NULL');
        $this->addSql('ALTER TABLE projects ALTER website_theme SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects DROP website_font_title');
        $this->addSql('ALTER TABLE projects DROP website_font_text');
        $this->addSql('ALTER TABLE projects ALTER website_theme DROP NOT NULL');
    }
}
