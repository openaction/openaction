<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211212130450 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE community_contacts_updates_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE community_contacts_updates (id BIGINT NOT NULL, contact_id BIGINT NOT NULL, email VARCHAR(250) DEFAULT NULL, type VARCHAR(20) NOT NULL, token VARCHAR(64) NOT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_49832E42D17F50A6 ON community_contacts_updates (uuid)');
        $this->addSql('CREATE INDEX IDX_49832E42E7A1254A ON community_contacts_updates (contact_id)');
        $this->addSql('COMMENT ON COLUMN community_contacts_updates.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE community_contacts_updates ADD CONSTRAINT FK_49832E42E7A1254A FOREIGN KEY (contact_id) REFERENCES community_contacts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE community_contacts_updates_id_seq CASCADE');
        $this->addSql('DROP TABLE community_contacts_updates');
    }
}
