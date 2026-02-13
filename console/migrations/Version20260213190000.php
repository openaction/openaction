<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260213190000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add contact payment subscriptions and relation from payments';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE community_contacts_subscriptions (
            id BIGSERIAL NOT NULL,
            contact_id BIGINT NOT NULL,
            type VARCHAR(64) NOT NULL,
            net_amount BIGINT NOT NULL,
            fees_amount BIGINT NOT NULL,
            currency VARCHAR(3) NOT NULL,
            payment_method VARCHAR(32) NOT NULL,
            interval_in_months INT NOT NULL,
            starts_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            ends_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
            active BOOLEAN NOT NULL,
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
            metadata JSONB DEFAULT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX community_contacts_subscriptions_contact_idx ON community_contacts_subscriptions (contact_id)');
        $this->addSql('CREATE INDEX community_contacts_subscriptions_active_idx ON community_contacts_subscriptions (active, ends_at)');
        $this->addSql('ALTER TABLE community_contacts_subscriptions ADD CONSTRAINT FK_CCS_CONTACT FOREIGN KEY (contact_id) REFERENCES community_contacts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('ALTER TABLE community_contacts_payments ADD subscription_id BIGINT DEFAULT NULL');
        $this->addSql('CREATE INDEX community_contacts_payments_subscription_idx ON community_contacts_payments (subscription_id)');
        $this->addSql('ALTER TABLE community_contacts_payments ADD CONSTRAINT FK_CCP_SUBSCRIPTION FOREIGN KEY (subscription_id) REFERENCES community_contacts_subscriptions (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_contacts_payments DROP CONSTRAINT FK_CCP_SUBSCRIPTION');
        $this->addSql('DROP INDEX community_contacts_payments_subscription_idx');
        $this->addSql('ALTER TABLE community_contacts_payments DROP subscription_id');

        $this->addSql('ALTER TABLE community_contacts_subscriptions DROP CONSTRAINT FK_CCS_CONTACT');
        $this->addSql('DROP TABLE community_contacts_subscriptions');
    }
}
