<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210726192533 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects ADD membership_settings JSON DEFAULT \'{}\'');
        $this->addSql('ALTER TABLE projects ALTER membership_settings DROP DEFAULT');
        $this->addSql('ALTER TABLE projects ALTER membership_settings SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects DROP membership_settings');
    }
}
