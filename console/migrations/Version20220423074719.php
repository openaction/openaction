<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220423074719 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE billing_orders ALTER mollie_id DROP NOT NULL');
        $this->addSql('ALTER TABLE organizations ADD print_subrogated BOOLEAN NOT NULL DEFAULT FALSE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations DROP print_subrogated');
        $this->addSql('ALTER TABLE billing_orders ALTER mollie_id SET NOT NULL');
    }
}
