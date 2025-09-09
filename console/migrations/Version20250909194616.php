<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250909194616 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // Keep only PR-related changes: ensure JSONB types
        $this->addSql('ALTER TABLE community_contacts ALTER settings_by_project TYPE JSONB USING settings_by_project::jsonb');
        $this->addSql('ALTER TABLE community_contacts_mandates ALTER metadata TYPE JSONB USING metadata::jsonb');
        $this->addSql('ALTER TABLE community_contacts_payments ALTER metadata TYPE JSONB USING metadata::jsonb');
    }
}
