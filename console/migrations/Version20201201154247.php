<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201201154247 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE community_ambiguities_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE community_ambiguities (id BIGINT NOT NULL, organization_id BIGINT NOT NULL, oldest_id BIGINT NOT NULL, newest_id BIGINT NOT NULL, ignored_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_84C4717A32C8A3DE ON community_ambiguities (organization_id)');
        $this->addSql('CREATE INDEX IDX_84C4717A678A8C6 ON community_ambiguities (oldest_id)');
        $this->addSql('CREATE INDEX IDX_84C4717A587A21FD ON community_ambiguities (newest_id)');
        $this->addSql('CREATE UNIQUE INDEX community_ambiguity_match ON community_ambiguities (oldest_id, newest_id)');
        $this->addSql('ALTER TABLE community_ambiguities ADD CONSTRAINT FK_84C4717A32C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_ambiguities ADD CONSTRAINT FK_84C4717A678A8C6 FOREIGN KEY (oldest_id) REFERENCES community_contacts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_ambiguities ADD CONSTRAINT FK_84C4717A587A21FD FOREIGN KEY (newest_id) REFERENCES community_contacts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE community_ambiguities_id_seq CASCADE');
        $this->addSql('DROP TABLE community_ambiguities');
    }
}
