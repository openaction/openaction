<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230830153508 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE community_printing_campaigns_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE community_printing_orders_id_seq CASCADE');
        $this->addSql('ALTER TABLE community_printing_campaigns DROP CONSTRAINT fk_990208143deeffdb');
        $this->addSql('ALTER TABLE community_printing_campaigns DROP CONSTRAINT fk_990208142df17ae6');
        $this->addSql('ALTER TABLE community_printing_campaigns DROP CONSTRAINT fk_99020814953c1c61');
        $this->addSql('ALTER TABLE community_printing_campaigns DROP CONSTRAINT fk_99020814cde46fdb');
        $this->addSql('ALTER TABLE community_printing_orders DROP CONSTRAINT fk_8e9e029f7ee8aed9');
        $this->addSql('ALTER TABLE community_printing_orders DROP CONSTRAINT fk_8e9e029f8d9f6d38');
        $this->addSql('ALTER TABLE community_printing_orders DROP CONSTRAINT fk_8e9e029f166d1f9c');
        $this->addSql('DROP TABLE community_printing_campaigns');
        $this->addSql('DROP TABLE community_printing_orders');
        $this->addSql('ALTER TABLE organizations DROP print_party');
        $this->addSql('ALTER TABLE organizations DROP print_subrogated');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE community_printing_campaigns_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE community_printing_orders_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE community_printing_campaigns (id BIGSERIAL NOT NULL, printing_order_id BIGINT NOT NULL, bat_id BIGINT DEFAULT NULL, source_id BIGINT DEFAULT NULL, preview_id BIGINT DEFAULT NULL, status JSON NOT NULL, production_status JSON NOT NULL, product VARCHAR(40) NOT NULL, quantity INT DEFAULT NULL, bat_validated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, printed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, source_error JSON DEFAULT NULL, bat_errors JSON DEFAULT NULL, bat_warnings JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_99020814cde46fdb ON community_printing_campaigns (preview_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_99020814953c1c61 ON community_printing_campaigns (source_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_990208142df17ae6 ON community_printing_campaigns (bat_id)');
        $this->addSql('CREATE INDEX idx_990208143deeffdb ON community_printing_campaigns (printing_order_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_99020814d17f50a6 ON community_printing_campaigns (uuid)');
        $this->addSql('COMMENT ON COLUMN community_printing_campaigns.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE community_printing_orders (id BIGSERIAL NOT NULL, delivery_address_file_id BIGINT DEFAULT NULL, order_id BIGINT DEFAULT NULL, project_id BIGINT NOT NULL, status JSON NOT NULL, with_enveloping BOOLEAN NOT NULL, delivery_addressed BOOLEAN NOT NULL, delivery_address_file_first_lines JSON DEFAULT NULL, delivery_address_list JSON DEFAULT NULL, delivery_use_mediapost BOOLEAN NOT NULL, delivery_main_address_name VARCHAR(100) DEFAULT NULL, delivery_main_address_street1 VARCHAR(100) DEFAULT NULL, delivery_main_address_street2 VARCHAR(100) DEFAULT NULL, delivery_main_address_zip_code VARCHAR(10) DEFAULT NULL, delivery_main_address_city VARCHAR(50) DEFAULT NULL, delivery_main_address_country VARCHAR(2) DEFAULT NULL, delivery_main_address_instructions TEXT DEFAULT NULL, delivery_poster_address_zip_code VARCHAR(10) DEFAULT NULL, delivery_poster_address_city VARCHAR(50) DEFAULT NULL, delivery_poster_address_country VARCHAR(2) DEFAULT NULL, delivery_poster_address_instructions TEXT DEFAULT NULL, recipient_department VARCHAR(10) DEFAULT NULL, recipient_circonscription VARCHAR(10) DEFAULT NULL, recipient_first_name VARCHAR(50) DEFAULT NULL, recipient_last_name VARCHAR(50) DEFAULT NULL, recipient_email VARCHAR(100) DEFAULT NULL, recipient_phone VARCHAR(50) DEFAULT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, recipient_candidate VARCHAR(150) DEFAULT NULL, delivery_main_address_provider VARCHAR(20) DEFAULT NULL, delivery_main_address_tracking_code VARCHAR(100) DEFAULT NULL, delivery_poster_address_name VARCHAR(100) DEFAULT NULL, delivery_poster_address_street1 VARCHAR(100) DEFAULT NULL, delivery_poster_address_street2 VARCHAR(100) DEFAULT NULL, delivery_poster_address_provider VARCHAR(20) DEFAULT NULL, delivery_poster_address_tracking_code VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_8e9e029f166d1f9c ON community_printing_orders (project_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_8e9e029f8d9f6d38 ON community_printing_orders (order_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_8e9e029f7ee8aed9 ON community_printing_orders (delivery_address_file_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_8e9e029fd17f50a6 ON community_printing_orders (uuid)');
        $this->addSql('COMMENT ON COLUMN community_printing_orders.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE community_printing_campaigns ADD CONSTRAINT fk_990208143deeffdb FOREIGN KEY (printing_order_id) REFERENCES community_printing_orders (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_printing_campaigns ADD CONSTRAINT fk_990208142df17ae6 FOREIGN KEY (bat_id) REFERENCES uploads (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_printing_campaigns ADD CONSTRAINT fk_99020814953c1c61 FOREIGN KEY (source_id) REFERENCES uploads (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_printing_campaigns ADD CONSTRAINT fk_99020814cde46fdb FOREIGN KEY (preview_id) REFERENCES uploads (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_printing_orders ADD CONSTRAINT fk_8e9e029f7ee8aed9 FOREIGN KEY (delivery_address_file_id) REFERENCES uploads (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_printing_orders ADD CONSTRAINT fk_8e9e029f8d9f6d38 FOREIGN KEY (order_id) REFERENCES billing_orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_printing_orders ADD CONSTRAINT fk_8e9e029f166d1f9c FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE organizations ADD print_party VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD print_subrogated BOOLEAN NOT NULL');
    }
}
