<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250909084406 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE community_contacts_payments (
            id BIGSERIAL NOT NULL,
            contact_id BIGINT NOT NULL,
            type VARCHAR(64) NOT NULL,
            net_amount BIGINT NOT NULL,
            fees_amount BIGINT NOT NULL,
            currency VARCHAR(3) NOT NULL,
            payment_provider VARCHAR(32) NOT NULL,
            payment_provider_details JSON DEFAULT NULL,
            payment_method VARCHAR(32) NOT NULL,
            captured_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
            failed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
            refunded_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
            canceled_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
            receipt_number VARCHAR(100) DEFAULT NULL,
            receipt_id BIGINT DEFAULT NULL,
            civility VARCHAR(50) DEFAULT NULL,
            first_name VARCHAR(150) DEFAULT NULL,
            last_name VARCHAR(150) DEFAULT NULL,
            email VARCHAR(250) DEFAULT NULL,
            street_address_line1 VARCHAR(150) DEFAULT NULL,
            street_address_line2 VARCHAR(150) DEFAULT NULL,
            city VARCHAR(150) DEFAULT NULL,
            postal_code VARCHAR(20) DEFAULT NULL,
            country_code VARCHAR(2) DEFAULT NULL,
            birthdate DATE DEFAULT NULL,
            phone VARCHAR(50) DEFAULT NULL,
            nationality VARCHAR(2) DEFAULT NULL,
            fiscal_country_code VARCHAR(2) DEFAULT NULL,
            membership_start_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
            membership_end_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
            membership_number VARCHAR(100) DEFAULT NULL,
            metadata JSONB DEFAULT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX community_contacts_payments_contact_idx ON community_contacts_payments (contact_id)');
        $this->addSql('CREATE INDEX community_contacts_payments_type_idx ON community_contacts_payments (type)');
        $this->addSql('ALTER TABLE community_contacts_payments ADD CONSTRAINT FK_CCP_CONTACT FOREIGN KEY (contact_id) REFERENCES community_contacts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_contacts_payments ADD CONSTRAINT FK_CCP_RECEIPT FOREIGN KEY (receipt_id) REFERENCES uploads (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('CREATE TABLE community_contacts_mandates (
            id BIGSERIAL NOT NULL,
            contact_id BIGINT NOT NULL,
            type VARCHAR(32) NOT NULL,
            label VARCHAR(255) NOT NULL,
            start_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            end_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            metadata JSONB DEFAULT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX community_contacts_mandates_contact_idx ON community_contacts_mandates (contact_id)');
        $this->addSql('ALTER TABLE community_contacts_mandates ADD CONSTRAINT FK_CCM_CONTACT FOREIGN KEY (contact_id) REFERENCES community_contacts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Commitments table
        $this->addSql('CREATE TABLE community_contacts_commitments (
            id BIGSERIAL NOT NULL,
            contact_id BIGINT NOT NULL,
            label VARCHAR(255) NOT NULL,
            start_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
            metadata JSON DEFAULT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX community_contacts_commitments_contact_idx ON community_contacts_commitments (contact_id)');
        $this->addSql('ALTER TABLE community_contacts_commitments ADD CONSTRAINT FK_CCC_CONTACT FOREIGN KEY (contact_id) REFERENCES community_contacts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Alter contacts table
        $this->addSql('ALTER TABLE community_contacts ADD is_deceased BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD recruited_by_id BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD social_instagram VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD social_tik_tok VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD social_bluesky VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD birth_name VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD birth_city VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD birth_country_code VARCHAR(2) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD CONSTRAINT FK_CC_RECRUITED_BY FOREIGN KEY (recruited_by_id) REFERENCES community_contacts (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX community_contacts_recruited_by_idx ON community_contacts (recruited_by_id)');
    }
}
