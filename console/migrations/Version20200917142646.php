<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200917142646 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects ADD social_snapchat VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE projects ALTER social_telegram TYPE VARCHAR(50)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects DROP social_snapchat');
        $this->addSql('ALTER TABLE projects ALTER social_telegram TYPE VARCHAR(32)');
    }
}
