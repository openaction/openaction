<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220505070715 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE billing_orders ADD lines JSON DEFAULT \'[]\' NOT NULL');
        $this->addSql('ALTER TABLE billing_orders ALTER lines DROP DEFAULT');
        $this->addSql('ALTER TABLE billing_quotes ADD recipient JSON DEFAULT \'[]\' NOT NULL');
        $this->addSql('ALTER TABLE billing_quotes ALTER recipient DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE billing_orders DROP lines');
        $this->addSql('ALTER TABLE billing_quotes DROP recipient');
    }
}
