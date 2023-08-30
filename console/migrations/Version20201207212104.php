<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201207212104 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations ADD quorum_token VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD quorum_default_city VARCHAR(100) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations DROP quorum_token');
        $this->addSql('ALTER TABLE organizations DROP quorum_default_city');
    }
}
