<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220905133526 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE community_imports_lines_id_seq CASCADE');
        $this->addSql('DROP TABLE community_imports_lines');
        $this->addSql('ALTER TABLE community_imports ADD job_id BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE community_imports DROP started_at');
        $this->addSql('ALTER TABLE community_imports DROP finished_at');
        $this->addSql('ALTER TABLE community_imports ADD CONSTRAINT FK_5C50844FBE04EA9 FOREIGN KEY (job_id) REFERENCES platform_jobs (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5C50844FBE04EA9 ON community_imports (job_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE community_imports_lines_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE community_imports_lines (id BIGSERIAL NOT NULL, import_id BIGINT NOT NULL, line JSON NOT NULL, processed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX import_line_processed_idx ON community_imports_lines (processed_at)');
        $this->addSql('CREATE INDEX idx_d1fa1697b6a263d9 ON community_imports_lines (import_id)');
        $this->addSql('ALTER TABLE community_imports_lines ADD CONSTRAINT fk_d1fa1697b6a263d9 FOREIGN KEY (import_id) REFERENCES community_imports (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_imports DROP CONSTRAINT FK_5C50844FBE04EA9');
        $this->addSql('ALTER TABLE community_imports ADD started_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE community_imports ADD finished_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE community_imports DROP job_id');
    }
}
