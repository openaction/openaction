<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210108231559 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE analytics_community_contact_creations_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE analytics_community_contact_creations (id BIGINT NOT NULL, contact_id BIGINT NOT NULL, organization_id BIGINT NOT NULL, project_id BIGINT NOT NULL, is_member BOOLEAN NOT NULL, receives_newsletter BOOLEAN NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1E7E6285E7A1254A ON analytics_community_contact_creations (contact_id)');
        $this->addSql('CREATE INDEX IDX_1E7E628532C8A3DE ON analytics_community_contact_creations (organization_id)');
        $this->addSql('CREATE INDEX IDX_1E7E6285166D1F9C ON analytics_community_contact_creations (project_id)');
        $this->addSql('ALTER TABLE analytics_community_contact_creations ADD CONSTRAINT FK_1E7E6285E7A1254A FOREIGN KEY (contact_id) REFERENCES community_contacts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE analytics_community_contact_creations ADD CONSTRAINT FK_1E7E628532C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE analytics_community_contact_creations ADD CONSTRAINT FK_1E7E6285166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER INDEX idx_67c528a2166d1f9c RENAME TO IDX_200F59BF166D1F9C');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE analytics_community_contact_creations_id_seq CASCADE');
        $this->addSql('DROP TABLE analytics_community_contact_creations');
        $this->addSql('ALTER INDEX idx_200f59bf166d1f9c RENAME TO idx_67c528a2166d1f9c');
    }
}
