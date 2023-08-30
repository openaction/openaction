<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200902220056 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects ADD website_access_user VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE projects ADD website_access_pass VARCHAR(50) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects DROP website_access_user');
        $this->addSql('ALTER TABLE projects DROP website_access_pass');
    }
}
