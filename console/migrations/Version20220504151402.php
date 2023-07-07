<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220504151402 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE billing_quotes_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE billing_quotes (id BIGINT NOT NULL, pdf_id BIGINT DEFAULT NULL, organization_id BIGINT NOT NULL, lines JSON NOT NULL, amount BIGINT NOT NULL, number BIGINT DEFAULT NULL, sent_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E46FAB9596901F54 ON billing_quotes (number)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E46FAB95D17F50A6 ON billing_quotes (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E46FAB95511FC912 ON billing_quotes (pdf_id)');
        $this->addSql('CREATE INDEX IDX_E46FAB9532C8A3DE ON billing_quotes (organization_id)');
        $this->addSql('COMMENT ON COLUMN billing_quotes.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE billing_quotes ADD CONSTRAINT FK_E46FAB95511FC912 FOREIGN KEY (pdf_id) REFERENCES uploads (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE billing_quotes ADD CONSTRAINT FK_E46FAB9532C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE billing_quotes_id_seq CASCADE');
        $this->addSql('DROP TABLE billing_quotes');
    }
}
