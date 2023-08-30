<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201206153519 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE registrations ADD locale VARCHAR(6) DEFAULT \'fr\'');
        $this->addSql('ALTER TABLE registrations ALTER locale DROP DEFAULT');
        $this->addSql('ALTER TABLE registrations ALTER locale SET NOT NULL');
        $this->addSql('ALTER TABLE users ALTER locale TYPE VARCHAR(6)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE registrations DROP locale');
        $this->addSql('ALTER TABLE users ALTER locale TYPE VARCHAR(10)');
    }
}
