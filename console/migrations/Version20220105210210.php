<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220105210210 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE billing_orders_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE billing_orders (id BIGINT NOT NULL, invoice_pdf_id BIGINT DEFAULT NULL, organization_id BIGINT NOT NULL, mollie_id VARCHAR(100) NOT NULL, recipient JSON NOT NULL, action JSON NOT NULL, amount BIGINT NOT NULL, paid_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, invoice_number BIGINT DEFAULT NULL, invoice_sent_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A0F5DEBE701427D5 ON billing_orders (mollie_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A0F5DEBE2DA68207 ON billing_orders (invoice_number)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A0F5DEBED17F50A6 ON billing_orders (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A0F5DEBEEA295441 ON billing_orders (invoice_pdf_id)');
        $this->addSql('CREATE INDEX IDX_A0F5DEBE32C8A3DE ON billing_orders (organization_id)');
        $this->addSql('COMMENT ON COLUMN billing_orders.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE billing_orders ADD CONSTRAINT FK_A0F5DEBEEA295441 FOREIGN KEY (invoice_pdf_id) REFERENCES uploads (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE billing_orders ADD CONSTRAINT FK_A0F5DEBE32C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE organizations ADD mollie_customer_id VARCHAR(100) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE billing_orders_id_seq CASCADE');
        $this->addSql('DROP TABLE billing_orders');
        $this->addSql('ALTER TABLE organizations DROP mollie_customer_id');
    }
}
