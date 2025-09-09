<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250909084406 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add new contact profile fields and related tables (safe if already present).';
    }

    public function up(Schema $schema): void
    {
        // New nullable columns on community_contacts
        $this->addSql('ALTER TABLE community_contacts ADD COLUMN IF NOT EXISTS social_instagram VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD COLUMN IF NOT EXISTS social_tik_tok VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD COLUMN IF NOT EXISTS social_bluesky VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD COLUMN IF NOT EXISTS is_deceased BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD COLUMN IF NOT EXISTS birth_name VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD COLUMN IF NOT EXISTS birth_city VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD COLUMN IF NOT EXISTS birth_country_code VARCHAR(2) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD COLUMN IF NOT EXISTS recruited_by_id BIGINT DEFAULT NULL');
        $this->addSql("DO $$ BEGIN IF NOT EXISTS (SELECT 1 FROM pg_constraint WHERE conname = 'FK_C106D0547318C8A5') THEN ALTER TABLE community_contacts ADD CONSTRAINT FK_C106D0547318C8A5 FOREIGN KEY (recruited_by_id) REFERENCES community_contacts (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE; END IF; END $$");
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_C106D0547318C8A5 ON community_contacts (recruited_by_id)');

        // Mandates table
        $this->addSql('CREATE TABLE IF NOT EXISTS community_contacts_mandates (
            id BIGSERIAL NOT NULL,
            contact_id BIGINT NOT NULL,
            type VARCHAR(255) NOT NULL,
            label VARCHAR(255) NOT NULL,
            start_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            end_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            metadata json DEFAULT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IF NOT EXISTS community_contacts_mandates_contact_idx ON community_contacts_mandates (contact_id)');
        $this->addSql("DO $$ BEGIN IF NOT EXISTS (SELECT 1 FROM pg_constraint WHERE conname = 'FK_E2A2A656E7A1254A') THEN ALTER TABLE community_contacts_mandates ADD CONSTRAINT FK_E2A2A656E7A1254A FOREIGN KEY (contact_id) REFERENCES community_contacts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE; END IF; END $$");
        $this->addSql("COMMENT ON COLUMN community_contacts_mandates.start_at IS '(DC2Type:datetime_immutable)'");
        $this->addSql("COMMENT ON COLUMN community_contacts_mandates.end_at IS '(DC2Type:datetime_immutable)'");

        // Commitments table
        $this->addSql('CREATE TABLE IF NOT EXISTS community_contacts_commitments (
            id BIGSERIAL NOT NULL,
            contact_id BIGINT NOT NULL,
            label VARCHAR(255) NOT NULL,
            start_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
            metadata JSON DEFAULT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IF NOT EXISTS community_contacts_commitments_contact_idx ON community_contacts_commitments (contact_id)');
        $this->addSql("DO $$ BEGIN IF NOT EXISTS (SELECT 1 FROM pg_constraint WHERE conname = 'FK_8B7319B9E7A1254A') THEN ALTER TABLE community_contacts_commitments ADD CONSTRAINT FK_8B7319B9E7A1254A FOREIGN KEY (contact_id) REFERENCES community_contacts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE; END IF; END $$");
        $this->addSql("COMMENT ON COLUMN community_contacts_commitments.start_at IS '(DC2Type:datetime_immutable)'");
    }

    public function down(Schema $schema): void
    {
        // No destructive down to keep test DB stable
    }
}
