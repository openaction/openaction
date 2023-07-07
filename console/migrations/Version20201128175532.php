<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201128175532 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE community_imports_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE community_imports_lines_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE community_imports (id BIGINT NOT NULL, file_id BIGINT NOT NULL, area_id BIGINT DEFAULT NULL, organization_id BIGINT NOT NULL, head JSON NOT NULL, delimiter VARCHAR(3) NOT NULL, started_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, finished_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5C50844FD17F50A6 ON community_imports (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5C50844F93CB796C ON community_imports (file_id)');
        $this->addSql('CREATE INDEX IDX_5C50844FBD0F409C ON community_imports (area_id)');
        $this->addSql('CREATE INDEX IDX_5C50844F32C8A3DE ON community_imports (organization_id)');
        $this->addSql('COMMENT ON COLUMN community_imports.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE community_imports_lines (id BIGINT NOT NULL, import_id BIGINT NOT NULL, line JSON NOT NULL, processed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D1FA1697B6A263D9 ON community_imports_lines (import_id)');
        $this->addSql('ALTER TABLE community_imports ADD CONSTRAINT FK_5C50844F93CB796C FOREIGN KEY (file_id) REFERENCES uploads (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_imports ADD CONSTRAINT FK_5C50844FBD0F409C FOREIGN KEY (area_id) REFERENCES areas (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_imports ADD CONSTRAINT FK_5C50844F32C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_imports_lines ADD CONSTRAINT FK_D1FA1697B6A263D9 FOREIGN KEY (import_id) REFERENCES community_imports (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_imports_lines DROP CONSTRAINT FK_D1FA1697B6A263D9');
        $this->addSql('DROP SEQUENCE community_imports_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE community_imports_lines_id_seq CASCADE');
        $this->addSql('DROP TABLE community_imports');
        $this->addSql('DROP TABLE community_imports_lines');
    }
}
