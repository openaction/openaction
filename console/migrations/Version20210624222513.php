<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210624222513 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects ADD website_custom_templates JSON DEFAULT \'{}\'');
        $this->addSql('ALTER TABLE projects ALTER website_custom_templates DROP DEFAULT');
        $this->addSql('ALTER TABLE projects ALTER website_custom_templates SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects DROP website_custom_templates');
    }
}
