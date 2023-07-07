<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201023220119 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users ALTER two_factor_enabled SET NOT NULL');
        $this->addSql('ALTER TABLE users ALTER notification_settings SET NOT NULL');
        $this->addSql('ALTER TABLE projects ADD website_main_intro_position VARCHAR(20) DEFAULT \'right\'');
        $this->addSql('ALTER TABLE projects ALTER website_main_intro_position DROP DEFAULT');
        $this->addSql('ALTER TABLE projects ALTER website_main_intro_position SET NOT NULL');
        $this->addSql('ALTER TABLE projects ADD website_main_intro_overlay BOOLEAN DEFAULT TRUE');
        $this->addSql('ALTER TABLE projects ALTER website_main_intro_overlay DROP DEFAULT');
        $this->addSql('ALTER TABLE projects ALTER website_main_intro_overlay SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users ALTER two_factor_enabled DROP NOT NULL');
        $this->addSql('ALTER TABLE users ALTER notification_settings DROP NOT NULL');
        $this->addSql('ALTER TABLE projects DROP website_main_intro_position');
        $this->addSql('ALTER TABLE projects DROP website_main_intro_overlay');
    }
}
