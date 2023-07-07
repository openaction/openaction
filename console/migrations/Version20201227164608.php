<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201227164608 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations ADD is_demo BOOLEAN DEFAULT FALSE');
        $this->addSql('ALTER TABLE organizations ALTER is_demo DROP DEFAULT');
        $this->addSql('ALTER TABLE organizations ALTER is_demo SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations DROP is_demo');
    }
}
