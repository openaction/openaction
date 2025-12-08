<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250922100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Brevo configuration fields on organizations';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations ADD brevo_api_key VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD brevo_sender_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD brevo_sender_email VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD brevo_list_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD brevo_campaign_folder_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD brevo_campaign_tag VARCHAR(100) DEFAULT NULL');
    }
}
