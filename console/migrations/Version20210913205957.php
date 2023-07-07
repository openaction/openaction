<?php

namespace Migrations;

use App\Platform\Plans;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210913205957 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations ADD billing_price_per_month INT DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD billing_name VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD billing_email VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD billing_address_street_line1 VARCHAR(200) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD billing_address_street_line2 VARCHAR(200) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD billing_address_postal_code VARCHAR(25) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD billing_address_city VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD billing_address_country VARCHAR(2) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD billing_tax_id VARCHAR(50) DEFAULT NULL');

        // Set default values
        $this->addSql('UPDATE organizations SET billing_name = name');
        $this->addSql('UPDATE organizations SET billing_price_per_month = 1900 WHERE subscription_plan = ?', [Plans::ESSENTIAL]);
        $this->addSql('UPDATE organizations SET billing_price_per_month = 3900 WHERE subscription_plan = ?', [Plans::STANDARD]);
        $this->addSql('UPDATE organizations SET billing_price_per_month = 7900 WHERE subscription_plan = ?', [Plans::PREMIUM]);
        $this->addSql('UPDATE organizations SET billing_price_per_month = 13900 WHERE subscription_plan = ?', [Plans::ORGANIZATION]);
        $this->addSql('UPDATE organizations SET billing_price_per_month = 0 WHERE subscription_trialing = TRUE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations DROP billing_price_per_month');
        $this->addSql('ALTER TABLE organizations DROP billing_name');
        $this->addSql('ALTER TABLE organizations DROP billing_email');
        $this->addSql('ALTER TABLE organizations DROP billing_address_street_line1');
        $this->addSql('ALTER TABLE organizations DROP billing_address_street_line2');
        $this->addSql('ALTER TABLE organizations DROP billing_address_postal_code');
        $this->addSql('ALTER TABLE organizations DROP billing_address_city');
        $this->addSql('ALTER TABLE organizations DROP billing_address_country');
        $this->addSql('ALTER TABLE organizations DROP billing_tax_id');
    }
}
