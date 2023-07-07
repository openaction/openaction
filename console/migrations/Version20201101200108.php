<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201101200108 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects ADD social_sharers JSON DEFAULT NULL');
        $this->addSql('UPDATE projects SET social_sharers = \'["facebook","twitter","telegram","whatsapp"]\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects DROP social_sharers');
    }
}
