<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220117132425 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE community_printing_campaigns_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE community_printing_campaigns (id BIGINT NOT NULL, delivery_address_file_id BIGINT DEFAULT NULL, order_id BIGINT DEFAULT NULL, bat_id BIGINT DEFAULT NULL, project_id BIGINT NOT NULL, status JSON NOT NULL, product JSON NOT NULL, qr_code JSON NOT NULL, with_enveloping BOOLEAN NOT NULL, delivery_addressed BOOLEAN NOT NULL, delivery_address_file_first_lines JSON DEFAULT NULL, delivery_address_list JSON DEFAULT NULL, delivery_quantity INT DEFAULT NULL, delivery_use_mediapost BOOLEAN NOT NULL, delivery_main_address_street1 VARCHAR(100) DEFAULT NULL, delivery_main_address_street2 VARCHAR(100) DEFAULT NULL, delivery_main_address_zip_code VARCHAR(10) DEFAULT NULL, delivery_main_address_city VARCHAR(50) DEFAULT NULL, delivery_main_address_country VARCHAR(2) DEFAULT NULL, delivery_main_phone VARCHAR(50) DEFAULT NULL, bat_validated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, printed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_99020814D17F50A6 ON community_printing_campaigns (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_990208147EE8AED9 ON community_printing_campaigns (delivery_address_file_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_990208148D9F6D38 ON community_printing_campaigns (order_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_990208142DF17AE6 ON community_printing_campaigns (bat_id)');
        $this->addSql('CREATE INDEX IDX_99020814166D1F9C ON community_printing_campaigns (project_id)');
        $this->addSql('COMMENT ON COLUMN community_printing_campaigns.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE community_printing_campaigns ADD CONSTRAINT FK_990208147EE8AED9 FOREIGN KEY (delivery_address_file_id) REFERENCES uploads (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_printing_campaigns ADD CONSTRAINT FK_990208148D9F6D38 FOREIGN KEY (order_id) REFERENCES billing_orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_printing_campaigns ADD CONSTRAINT FK_990208142DF17AE6 FOREIGN KEY (bat_id) REFERENCES uploads (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_printing_campaigns ADD CONSTRAINT FK_99020814166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE community_printing_campaigns_id_seq CASCADE');
        $this->addSql('DROP TABLE community_printing_campaigns');
    }
}
