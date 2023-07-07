<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220410081620 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DROP TABLE community_printing_campaigns_unique_scans');
        $this->addSql('DROP TABLE community_printing_campaigns_unique_documents');
        $this->addSql('DROP TABLE community_printing_campaigns_common_scans');
        $this->addSql('DROP TABLE community_printing_campaigns');
        $this->addSql('DROP SEQUENCE community_printing_campaigns_unique_scans_id_seq');
        $this->addSql('DROP SEQUENCE community_printing_campaigns_unique_documents_id_seq');
        $this->addSql('DROP SEQUENCE community_printing_campaigns_common_scans_id_seq');
        $this->addSql('DROP SEQUENCE community_printing_campaigns_id_seq');
        $this->addSql('CREATE SEQUENCE community_printing_campaigns_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE community_printing_campaigns_common_scans_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE community_printing_campaigns_unique_documents_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE community_printing_campaigns_unique_scans_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE community_printing_orders_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE community_printing_campaigns (id BIGINT NOT NULL, printing_order_id BIGINT NOT NULL, bat_id BIGINT DEFAULT NULL, status JSON NOT NULL, production_status JSON NOT NULL, product JSON NOT NULL, qr_code JSON NOT NULL, quantity INT DEFAULT NULL, bat_validated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, printed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_99020814D17F50A6 ON community_printing_campaigns (uuid)');
        $this->addSql('CREATE INDEX IDX_990208143DEEFFDB ON community_printing_campaigns (printing_order_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_990208142DF17AE6 ON community_printing_campaigns (bat_id)');
        $this->addSql('COMMENT ON COLUMN community_printing_campaigns.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE community_printing_campaigns_common_scans (id BIGINT NOT NULL, campaign_id BIGINT NOT NULL, platform VARCHAR(30) DEFAULT NULL, browser VARCHAR(30) DEFAULT NULL, country VARCHAR(2) DEFAULT NULL, scanned_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E0270B64F639F774 ON community_printing_campaigns_common_scans (campaign_id)');
        $this->addSql('CREATE TABLE community_printing_campaigns_unique_documents (id BIGINT NOT NULL, campaign_id BIGINT NOT NULL, serial_number BIGINT NOT NULL, label VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6F7613CFF639F774 ON community_printing_campaigns_unique_documents (campaign_id)');
        $this->addSql('CREATE INDEX community_printing_campaigns_unique_documents_serial_number ON community_printing_campaigns_unique_documents (serial_number)');
        $this->addSql('CREATE TABLE community_printing_campaigns_unique_scans (id BIGINT NOT NULL, document_id BIGINT NOT NULL, platform VARCHAR(30) DEFAULT NULL, browser VARCHAR(30) DEFAULT NULL, country VARCHAR(2) DEFAULT NULL, scanned_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6EEFF4BEC33F7837 ON community_printing_campaigns_unique_scans (document_id)');
        $this->addSql('CREATE TABLE community_printing_orders (id BIGINT NOT NULL, delivery_address_file_id BIGINT DEFAULT NULL, order_id BIGINT DEFAULT NULL, project_id BIGINT NOT NULL, status JSON NOT NULL, with_enveloping BOOLEAN NOT NULL, delivery_addressed BOOLEAN NOT NULL, delivery_address_file_first_lines JSON DEFAULT NULL, delivery_address_list JSON DEFAULT NULL, delivery_use_mediapost BOOLEAN NOT NULL, delivery_main_address_name VARCHAR(100) DEFAULT NULL, delivery_main_address_street1 VARCHAR(100) DEFAULT NULL, delivery_main_address_street2 VARCHAR(100) DEFAULT NULL, delivery_main_address_zip_code VARCHAR(10) DEFAULT NULL, delivery_main_address_city VARCHAR(50) DEFAULT NULL, delivery_main_address_country VARCHAR(2) DEFAULT NULL, delivery_main_address_instructions TEXT DEFAULT NULL, preview_address_name VARCHAR(100) DEFAULT NULL, preview_address_street1 VARCHAR(100) DEFAULT NULL, preview_address_street2 VARCHAR(100) DEFAULT NULL, preview_address_zip_code VARCHAR(10) DEFAULT NULL, preview_address_city VARCHAR(50) DEFAULT NULL, preview_address_country VARCHAR(2) DEFAULT NULL, preview_address_instructions TEXT DEFAULT NULL, recipient_department VARCHAR(200) DEFAULT NULL, recipient_circonscription VARCHAR(200) DEFAULT NULL, recipient_first_name VARCHAR(50) DEFAULT NULL, recipient_last_name VARCHAR(50) DEFAULT NULL, recipient_email VARCHAR(100) DEFAULT NULL, recipient_phone VARCHAR(50) DEFAULT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8E9E029FD17F50A6 ON community_printing_orders (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8E9E029F7EE8AED9 ON community_printing_orders (delivery_address_file_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8E9E029F8D9F6D38 ON community_printing_orders (order_id)');
        $this->addSql('CREATE INDEX IDX_8E9E029F166D1F9C ON community_printing_orders (project_id)');
        $this->addSql('COMMENT ON COLUMN community_printing_orders.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE community_printing_campaigns ADD CONSTRAINT FK_990208143DEEFFDB FOREIGN KEY (printing_order_id) REFERENCES community_printing_orders (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_printing_campaigns ADD CONSTRAINT FK_990208142DF17AE6 FOREIGN KEY (bat_id) REFERENCES uploads (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_printing_campaigns_common_scans ADD CONSTRAINT FK_E0270B64F639F774 FOREIGN KEY (campaign_id) REFERENCES community_printing_campaigns (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_printing_campaigns_unique_documents ADD CONSTRAINT FK_6F7613CFF639F774 FOREIGN KEY (campaign_id) REFERENCES community_printing_campaigns (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_printing_campaigns_unique_scans ADD CONSTRAINT FK_6EEFF4BEC33F7837 FOREIGN KEY (document_id) REFERENCES community_printing_campaigns_unique_documents (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_printing_orders ADD CONSTRAINT FK_8E9E029F7EE8AED9 FOREIGN KEY (delivery_address_file_id) REFERENCES uploads (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_printing_orders ADD CONSTRAINT FK_8E9E029F8D9F6D38 FOREIGN KEY (order_id) REFERENCES billing_orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_printing_orders ADD CONSTRAINT FK_8E9E029F166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
    }
}
