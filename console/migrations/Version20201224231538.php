<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201224231538 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX stripe_customer_id_idx');
        $this->addSql('DROP INDEX stripe_subscription_id_idx');
        $this->addSql('ALTER TABLE organizations DROP stripe_customer_id');
        $this->addSql('ALTER TABLE organizations DROP stripe_subscription_id');
        $this->addSql('ALTER TABLE organizations DROP active');
        $this->addSql('ALTER TABLE organizations RENAME COLUMN trialing TO subscription_trialing');
        $this->addSql('ALTER TABLE organizations RENAME COLUMN current_period_end TO subscription_current_period_end');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations ADD stripe_customer_id VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD stripe_subscription_id VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD trialing BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE organizations RENAME COLUMN subscription_trialing TO active');
        $this->addSql('ALTER TABLE organizations RENAME COLUMN subscription_current_period_end TO current_period_end');
        $this->addSql('CREATE INDEX stripe_customer_id_idx ON organizations (stripe_customer_id)');
        $this->addSql('CREATE INDEX stripe_subscription_id_idx ON organizations (stripe_subscription_id)');
    }
}
