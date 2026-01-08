<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260108212217 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE community_emailing_campaigns_batches (id BIGSERIAL NOT NULL, campaign_id BIGINT NOT NULL, email_provider VARCHAR(30) NOT NULL, payload JSONB NOT NULL, sent_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CF4CFABEF639F774 ON community_emailing_campaigns_batches (campaign_id)');
        $this->addSql('ALTER TABLE community_emailing_campaigns_batches ADD CONSTRAINT FK_CF4CFABEF639F774 FOREIGN KEY (campaign_id) REFERENCES community_emailing_campaigns (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
    }
}
