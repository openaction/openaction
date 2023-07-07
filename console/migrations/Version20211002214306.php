<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211002214306 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects ADD membership_main_page TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE projects RENAME COLUMN membership_settings TO membership_form_settings');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects DROP membership_main_page');
        $this->addSql('ALTER TABLE projects RENAME COLUMN membership_form_settings TO membership_settings');
    }
}
