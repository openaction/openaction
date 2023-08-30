<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200724103117 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects ADD website_theme VARCHAR(25) DEFAULT NULL');
        $this->addSql('ALTER TABLE projects ADD website_theme_options JSON DEFAULT \'[]\'');
        $this->addSql('ALTER TABLE projects ALTER website_theme_options DROP DEFAULT');
        $this->addSql('ALTER TABLE projects ALTER website_theme_options SET NOT NULL');
        $this->addSql('ALTER TABLE projects ADD website_custom_css TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE projects ADD emailing_custom_css TEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects DROP website_theme');
        $this->addSql('ALTER TABLE projects DROP website_theme_options');
        $this->addSql('ALTER TABLE projects DROP website_custom_css');
        $this->addSql('ALTER TABLE projects DROP emailing_custom_css');
    }
}
