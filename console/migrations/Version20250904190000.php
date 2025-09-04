<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250904190000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // Organization: application fee config
        $this->addSql("ALTER TABLE organizations ADD mollie_app_fee_enabled BOOLEAN DEFAULT true NOT NULL");
        $this->addSql("ALTER TABLE organizations ADD mollie_app_fee_percent NUMERIC(5, 2) DEFAULT '1.00' NOT NULL");

        // Project: Mollie + donations + paying memberships settings
        $this->addSql("ALTER TABLE projects ADD mollie_profile_id VARCHAR(40) DEFAULT NULL");
        $this->addSql("ALTER TABLE projects ADD mollie_currency VARCHAR(3) DEFAULT NULL");
        $this->addSql("ALTER TABLE projects ADD mollie_allowed_methods JSON DEFAULT NULL");

        $this->addSql("ALTER TABLE projects ADD donation_recommended_amounts JSON DEFAULT NULL");
        $this->addSql("ALTER TABLE projects ADD donation_allow_custom BOOLEAN DEFAULT true NOT NULL");
        $this->addSql("ALTER TABLE projects ADD donation_custom_min BIGINT DEFAULT NULL");
        $this->addSql("ALTER TABLE projects ADD donation_custom_max BIGINT DEFAULT NULL");
        $this->addSql("ALTER TABLE projects ADD donation_show_recurring BOOLEAN DEFAULT true NOT NULL");
        $this->addSql("ALTER TABLE projects ADD donation_default_recurring BOOLEAN DEFAULT false NOT NULL");

        $this->addSql("ALTER TABLE projects ADD paying_memberships_enabled BOOLEAN DEFAULT false NOT NULL");
        $this->addSql("ALTER TABLE projects ADD membership_allow_custom_amount BOOLEAN DEFAULT false NOT NULL");
        $this->addSql("ALTER TABLE projects ADD membership_custom_min BIGINT DEFAULT NULL");
        $this->addSql("ALTER TABLE projects ADD membership_custom_max BIGINT DEFAULT NULL");
        $this->addSql("ALTER TABLE projects ADD membership_show_auto_renew BOOLEAN DEFAULT true NOT NULL");
        $this->addSql("ALTER TABLE projects ADD membership_default_auto_renew BOOLEAN DEFAULT false NOT NULL");

        // Integration: Mollie OAuth connections (1:1 with organization)
        $this->addSql(<<<SQL
CREATE TABLE integration_mollie_oauth_connections (
    id BIGSERIAL NOT NULL,
    organization_id BIGINT NOT NULL,
    client_id VARCHAR(100) DEFAULT NULL,
    scopes JSON DEFAULT NULL,
    refresh_token TEXT NOT NULL,
    access_token TEXT DEFAULT NULL,
    access_token_expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
    mollie_organization_id VARCHAR(50) DEFAULT NULL,
    mollie_organization_name VARCHAR(200) DEFAULT NULL,
    testmode BOOLEAN NOT NULL DEFAULT FALSE,
    capabilities JSON DEFAULT NULL,
    PRIMARY KEY(id)
)
SQL);
        $this->addSql("CREATE UNIQUE INDEX UNIQ_MOLLIE_OAUTH_ORG ON integration_mollie_oauth_connections (organization_id)");
        $this->addSql("ALTER TABLE integration_mollie_oauth_connections ADD CONSTRAINT FK_MOLLIE_OAUTH_ORG FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");

        // Donations
        $this->addSql(<<<SQL
CREATE TABLE donations (
    id BIGSERIAL NOT NULL,
    organization_id BIGINT NOT NULL,
    project_id BIGINT DEFAULT NULL,
    contact_id BIGINT NOT NULL,
    amount BIGINT NOT NULL,
    currency VARCHAR(3) NOT NULL,
    is_recurring BOOLEAN NOT NULL DEFAULT FALSE,
    frequency VARCHAR(10) DEFAULT NULL,
    status VARCHAR(20) NOT NULL,
    method VARCHAR(50) DEFAULT NULL,
    mollie_payment_id VARCHAR(100) DEFAULT NULL,
    mollie_subscription_id VARCHAR(100) DEFAULT NULL,
    description VARCHAR(255) DEFAULT NULL,
    metadata JSON DEFAULT NULL,
    origin VARCHAR(10) NOT NULL DEFAULT 'mollie',
    paid_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    PRIMARY KEY(id)
)
SQL);
        $this->addSql("CREATE UNIQUE INDEX UNIQ_DONATIONS_MOLLIE_PAYMENT ON donations (mollie_payment_id)");
        $this->addSql("CREATE INDEX IDX_DONATIONS_ORG ON donations (organization_id)");
        $this->addSql("CREATE INDEX IDX_DONATIONS_PROJECT ON donations (project_id)");
        $this->addSql("CREATE INDEX IDX_DONATIONS_CONTACT ON donations (contact_id)");
        $this->addSql("CREATE INDEX IDX_DONATIONS_STATUS ON donations (status)");
        $this->addSql("ALTER TABLE donations ADD CONSTRAINT FK_DONATIONS_ORG FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE donations ADD CONSTRAINT FK_DONATIONS_PROJECT FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE donations ADD CONSTRAINT FK_DONATIONS_CONTACT FOREIGN KEY (contact_id) REFERENCES community_contacts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");

        // Membership plans
        $this->addSql(<<<SQL
CREATE TABLE membership_plans (
    id BIGSERIAL NOT NULL,
    project_id BIGINT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT DEFAULT NULL,
    price BIGINT NOT NULL,
    currency VARCHAR(3) NOT NULL,
    interval VARCHAR(10) NOT NULL,
    image_id BIGINT DEFAULT NULL,
    active BOOLEAN NOT NULL DEFAULT TRUE,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    PRIMARY KEY(id)
)
SQL);
        $this->addSql("CREATE INDEX IDX_MEMBERSHIP_PLANS_PROJECT ON membership_plans (project_id)");
        $this->addSql("ALTER TABLE membership_plans ADD CONSTRAINT FK_MEMBERSHIP_PLANS_PROJECT FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE membership_plans ADD CONSTRAINT FK_MEMBERSHIP_PLANS_IMAGE FOREIGN KEY (image_id) REFERENCES uploads (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE");

        // Contact memberships
        $this->addSql(<<<SQL
CREATE TABLE contact_memberships (
    id BIGSERIAL NOT NULL,
    organization_id BIGINT NOT NULL,
    project_id BIGINT NOT NULL,
    contact_id BIGINT NOT NULL,
    plan_id BIGINT DEFAULT NULL,
    amount BIGINT NOT NULL,
    currency VARCHAR(3) NOT NULL,
    interval VARCHAR(10) NOT NULL,
    status VARCHAR(20) NOT NULL,
    origin VARCHAR(10) NOT NULL,
    auto_renew BOOLEAN NOT NULL DEFAULT FALSE,
    current_period_start TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
    current_period_end TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
    mollie_subscription_id VARCHAR(100) DEFAULT NULL,
    mollie_customer_id VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    PRIMARY KEY(id)
)
SQL);
        $this->addSql("CREATE INDEX IDX_CONTACT_MEMBERSHIPS_ORG ON contact_memberships (organization_id)");
        $this->addSql("CREATE INDEX IDX_CONTACT_MEMBERSHIPS_PROJECT ON contact_memberships (project_id)");
        $this->addSql("CREATE INDEX IDX_CONTACT_MEMBERSHIPS_CONTACT ON contact_memberships (contact_id)");
        $this->addSql("CREATE INDEX IDX_CONTACT_MEMBERSHIPS_STATUS ON contact_memberships (status)");
        $this->addSql("ALTER TABLE contact_memberships ADD CONSTRAINT FK_CONTACT_MEMBERSHIPS_ORG FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE contact_memberships ADD CONSTRAINT FK_CONTACT_MEMBERSHIPS_PROJECT FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE contact_memberships ADD CONSTRAINT FK_CONTACT_MEMBERSHIPS_CONTACT FOREIGN KEY (contact_id) REFERENCES community_contacts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE contact_memberships ADD CONSTRAINT FK_CONTACT_MEMBERSHIPS_PLAN FOREIGN KEY (plan_id) REFERENCES membership_plans (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE");
    }
}

