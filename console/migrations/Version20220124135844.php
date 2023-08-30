<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220124135844 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users ADD partner_name VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD partner_menu JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users DROP partner_name');
        $this->addSql('ALTER TABLE users DROP partner_menu');
    }
}
