<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220506105736 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE billing_orders ADD company VARCHAR(30) DEFAULT \'citipo\' NOT NULL');
        $this->addSql('ALTER TABLE billing_orders ALTER company DROP DEFAULT');
        $this->addSql('ALTER TABLE billing_quotes ADD company VARCHAR(30) DEFAULT \'citipo\' NOT NULL');
        $this->addSql('ALTER TABLE billing_quotes ALTER company DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE billing_orders DROP company');
        $this->addSql('ALTER TABLE billing_quotes DROP company');
    }
}
