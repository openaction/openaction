<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260313000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Brevo send state, token, dedup key and remote-step checkpoints for emailing campaigns.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE community_emailing_campaigns ADD brevo_send_state VARCHAR(20) DEFAULT 'draft' NOT NULL");
        $this->addSql('ALTER TABLE community_emailing_campaigns ADD send_token VARCHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_emailing_campaigns ADD brevo_dedup_key VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_emailing_campaigns ADD brevo_remote_created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE community_emailing_campaigns ADD brevo_remote_sent_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');

        $this->addSql(
            "UPDATE community_emailing_campaigns
             SET brevo_send_state = 'sent',
                 brevo_remote_created_at = COALESCE(brevo_remote_created_at, sent_at),
                 brevo_remote_sent_at = COALESCE(brevo_remote_sent_at, sent_at)
             WHERE sent_at IS NOT NULL AND external_id IS NOT NULL"
        );

        $this->addSql("ALTER TABLE community_emailing_campaigns ADD CONSTRAINT community_emailing_campaigns_brevo_send_state_chk CHECK (brevo_send_state IN ('draft', 'queued', 'sending', 'sent', 'failed'))");
        $this->addSql('CREATE UNIQUE INDEX community_emailing_campaigns_send_token_uidx ON community_emailing_campaigns (send_token) WHERE send_token IS NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX community_emailing_campaigns_brevo_dedup_key_uidx ON community_emailing_campaigns (brevo_dedup_key) WHERE brevo_dedup_key IS NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX community_emailing_campaigns_send_token_uidx');
        $this->addSql('DROP INDEX community_emailing_campaigns_brevo_dedup_key_uidx');
        $this->addSql('ALTER TABLE community_emailing_campaigns DROP CONSTRAINT community_emailing_campaigns_brevo_send_state_chk');
        $this->addSql('ALTER TABLE community_emailing_campaigns DROP brevo_send_state');
        $this->addSql('ALTER TABLE community_emailing_campaigns DROP send_token');
        $this->addSql('ALTER TABLE community_emailing_campaigns DROP brevo_dedup_key');
        $this->addSql('ALTER TABLE community_emailing_campaigns DROP brevo_remote_created_at');
        $this->addSql('ALTER TABLE community_emailing_campaigns DROP brevo_remote_sent_at');
    }
}
