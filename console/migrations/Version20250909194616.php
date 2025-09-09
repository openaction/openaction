<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250909194616 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE analytics_community_contact_creations_id_seq CASCADE');
        $this->addSql('DROP INDEX community_contacts_email_organization_unique_idx');
        $this->addSql('DROP INDEX community_contacts_settings_by_projects_idx');
        $this->addSql('ALTER TABLE community_contacts ALTER settings_by_project TYPE json');
        $this->addSql('ALTER INDEX community_contacts_recruited_by_idx RENAME TO IDX_C106D0547318C8A5');
        $this->addSql('ALTER TABLE community_contacts_commitments ALTER start_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN community_contacts_commitments.start_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE community_contacts_mandates ALTER type TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE community_contacts_mandates ALTER start_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE community_contacts_mandates ALTER end_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE community_contacts_mandates ALTER metadata TYPE json');
        $this->addSql('COMMENT ON COLUMN community_contacts_mandates.start_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN community_contacts_mandates.end_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE community_contacts_payments ALTER type TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE community_contacts_payments ALTER payment_provider TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE community_contacts_payments ALTER payment_method TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE community_contacts_payments ALTER captured_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE community_contacts_payments ALTER failed_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE community_contacts_payments ALTER refunded_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE community_contacts_payments ALTER canceled_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE community_contacts_payments ALTER membership_start_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE community_contacts_payments ALTER membership_end_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE community_contacts_payments ALTER metadata TYPE json');
        $this->addSql('COMMENT ON COLUMN community_contacts_payments.captured_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN community_contacts_payments.failed_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN community_contacts_payments.refunded_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN community_contacts_payments.canceled_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN community_contacts_payments.membership_start_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN community_contacts_payments.membership_end_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DBD5401F2B5CA896 ON community_contacts_payments (receipt_id)');
        $this->addSql('DROP INDEX community_emailing_campaigns_messages_unique_idx');
        $this->addSql('ALTER TABLE community_emailing_campaigns_messages ALTER unsubscribed DROP DEFAULT');
        $this->addSql('DROP INDEX community_emailing_campaigns_messages_logs_type');
        $this->addSql('ALTER TABLE community_imports ALTER job_id SET NOT NULL');
        $this->addSql('DROP INDEX community_tags_name_organization_idx');
        $this->addSql('DROP INDEX community_texting_campaigns_messages_unique_idx');
        $this->addSql('ALTER TABLE organizations ALTER email_enable_open_tracking DROP DEFAULT');
        $this->addSql('ALTER TABLE organizations ALTER email_enable_click_tracking DROP DEFAULT');
        $this->addSql('ALTER TABLE uploads DROP last_accessed_at');
    }
}
