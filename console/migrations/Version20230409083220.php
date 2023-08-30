<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230409083220 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE community_contacts_logs (id BIGSERIAL NOT NULL, contact_id BIGINT NOT NULL, type VARCHAR(30) NOT NULL, source VARCHAR(50) DEFAULT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9001FC68E7A1254A ON community_contacts_logs (contact_id)');
        $this->addSql('ALTER TABLE community_contacts_logs ADD CONSTRAINT FK_9001FC68E7A1254A FOREIGN KEY (contact_id) REFERENCES community_contacts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_contacts_logs DROP CONSTRAINT FK_9001FC68E7A1254A');
        $this->addSql('DROP TABLE community_contacts_logs');
    }
}
