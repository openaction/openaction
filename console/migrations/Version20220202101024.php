<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220202101024 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE community_printing_campaigns_common_scans_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE community_printing_campaigns_unique_documents_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE community_printing_campaigns_unique_scans_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE community_printing_campaigns_common_scans (id BIGINT NOT NULL, campaign_id BIGINT NOT NULL, platform VARCHAR(30) DEFAULT NULL, browser VARCHAR(30) DEFAULT NULL, country VARCHAR(2) DEFAULT NULL, scanned_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E0270B64F639F774 ON community_printing_campaigns_common_scans (campaign_id)');
        $this->addSql('CREATE TABLE community_printing_campaigns_unique_documents (id BIGINT NOT NULL, campaign_id BIGINT NOT NULL, serial_number BIGINT NOT NULL, label VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6F7613CFF639F774 ON community_printing_campaigns_unique_documents (campaign_id)');
        $this->addSql('CREATE INDEX community_printing_campaigns_unique_documents_serial_number ON community_printing_campaigns_unique_documents (serial_number)');
        $this->addSql('CREATE TABLE community_printing_campaigns_unique_scans (id BIGINT NOT NULL, document_id BIGINT NOT NULL, platform VARCHAR(30) DEFAULT NULL, browser VARCHAR(30) DEFAULT NULL, country VARCHAR(2) DEFAULT NULL, scanned_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6EEFF4BEC33F7837 ON community_printing_campaigns_unique_scans (document_id)');
        $this->addSql('ALTER TABLE community_printing_campaigns_common_scans ADD CONSTRAINT FK_E0270B64F639F774 FOREIGN KEY (campaign_id) REFERENCES community_printing_campaigns (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_printing_campaigns_unique_documents ADD CONSTRAINT FK_6F7613CFF639F774 FOREIGN KEY (campaign_id) REFERENCES community_printing_campaigns (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_printing_campaigns_unique_scans ADD CONSTRAINT FK_6EEFF4BEC33F7837 FOREIGN KEY (document_id) REFERENCES community_printing_campaigns_unique_documents (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_printing_campaigns_unique_scans DROP CONSTRAINT FK_6EEFF4BEC33F7837');
        $this->addSql('DROP SEQUENCE community_printing_campaigns_common_scans_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE community_printing_campaigns_unique_documents_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE community_printing_campaigns_unique_scans_id_seq CASCADE');
        $this->addSql('DROP TABLE community_printing_campaigns_common_scans');
        $this->addSql('DROP TABLE community_printing_campaigns_unique_documents');
        $this->addSql('DROP TABLE community_printing_campaigns_unique_scans');
    }
}
