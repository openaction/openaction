<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260211150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Drop legacy Brevo organization-level fields';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations DROP brevo_sender_id');
        $this->addSql('ALTER TABLE organizations DROP brevo_list_id');
        $this->addSql('ALTER TABLE organizations DROP brevo_campaign_folder_id');
        $this->addSql('ALTER TABLE organizations DROP brevo_campaign_tag');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations ADD brevo_sender_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD brevo_list_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD brevo_campaign_folder_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD brevo_campaign_tag VARCHAR(100) DEFAULT NULL');
    }
}
