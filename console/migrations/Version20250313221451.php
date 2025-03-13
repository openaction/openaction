<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250313221451 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_emailing_campaigns ADD external_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD email_provider VARCHAR(40) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD mailchimp_server_prefix VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD mailchimp_api_key VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD mailchimp_audience_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations DROP email_provider');
        $this->addSql('ALTER TABLE organizations DROP mailchimp_server_prefix');
        $this->addSql('ALTER TABLE organizations DROP mailchimp_api_key');
        $this->addSql('ALTER TABLE organizations DROP mailchimp_audience_name');
        $this->addSql('ALTER TABLE community_emailing_campaigns DROP external_id');
    }
}
