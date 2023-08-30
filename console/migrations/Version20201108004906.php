<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201108004906 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects ADD legal_publisher_name VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE projects ADD legal_publisher_role VARCHAR(100) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects DROP legal_publisher_name');
        $this->addSql('ALTER TABLE projects DROP legal_publisher_role');
    }
}
