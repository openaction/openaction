<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210112220002 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE community_emailing_campaigns_messages_logs_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE community_emailing_campaigns_messages_logs (id BIGINT NOT NULL, message_id BIGINT NOT NULL, type VARCHAR(20) NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1651F3D9537A1329 ON community_emailing_campaigns_messages_logs (message_id)');
        $this->addSql('ALTER TABLE community_emailing_campaigns_messages_logs ADD CONSTRAINT FK_1651F3D9537A1329 FOREIGN KEY (message_id) REFERENCES community_emailing_campaigns_messages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Migrate existing messages
        $this->addSql("
            INSERT INTO community_emailing_campaigns_messages_logs (id, message_id, type, date)
            SELECT
               nextval('community_emailing_campaigns_messages_logs_id_seq'),
               id AS message_id,
               'open',
               opened_at AS date
            FROM community_emailing_campaigns_messages
            WHERE opened_at IS NOT NULL
        ");

        $this->addSql("
            INSERT INTO community_emailing_campaigns_messages_logs (id, message_id, type, date)
            SELECT
               nextval('community_emailing_campaigns_messages_logs_id_seq'),
               id AS message_id,
               'click',
               clicked_at AS date
            FROM community_emailing_campaigns_messages
            WHERE clicked_at IS NOT NULL
        ");

        $this->addSql('ALTER TABLE community_emailing_campaigns_messages DROP opened_at');
        $this->addSql('ALTER TABLE community_emailing_campaigns_messages DROP clicked_at');
        $this->addSql('ALTER TABLE community_emailing_campaigns_messages DROP unsubscribed');
        $this->addSql('ALTER TABLE community_emailing_campaigns_messages DROP unsubscribed_at');
        $this->addSql('ALTER TABLE community_emailing_campaigns_messages DROP marked_spam');
        $this->addSql('ALTER TABLE community_emailing_campaigns_messages DROP marked_spam_at');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE community_emailing_campaigns_messages_logs_id_seq CASCADE');
        $this->addSql('DROP TABLE community_emailing_campaigns_messages_logs');
        $this->addSql('ALTER TABLE community_emailing_campaigns_messages ADD opened_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE community_emailing_campaigns_messages ADD clicked_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE community_emailing_campaigns_messages ADD unsubscribed BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE community_emailing_campaigns_messages ADD unsubscribed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE community_emailing_campaigns_messages ADD marked_spam BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE community_emailing_campaigns_messages ADD marked_spam_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    }
}
