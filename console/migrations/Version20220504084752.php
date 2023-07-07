<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220504084752 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_printing_campaigns ADD bat_errors JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE community_printing_campaigns ADD bat_warnings JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE community_printing_campaigns DROP bat_error');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_printing_campaigns ADD bat_error VARCHAR(200) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_printing_campaigns DROP bat_errors');
        $this->addSql('ALTER TABLE community_printing_campaigns DROP bat_warnings');
    }
}
