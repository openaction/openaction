<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240214144727 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE project_content_imports (id BIGSERIAL NOT NULL, organization_id BIGINT NOT NULL, source VARCHAR(20) NOT NULL, settings JSON DEFAULT NULL, file_id BIGINT NOT NULL, job_id BIGINT NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9FAC415FD17F50A6 ON project_content_imports (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9FAC415F93CB796C ON project_content_imports (file_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9FAC415FBE04EA9 ON project_content_imports (job_id)');
        $this->addSql('CREATE INDEX IDX_9FAC415F32C8A3DE ON project_content_imports (organization_id)');
        $this->addSql('COMMENT ON COLUMN project_content_imports.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE project_content_imports ADD CONSTRAINT FK_9FAC415F93CB796C FOREIGN KEY (file_id) REFERENCES uploads (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project_content_imports ADD CONSTRAINT FK_9FAC415FBE04EA9 FOREIGN KEY (job_id) REFERENCES platform_jobs (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project_content_imports ADD CONSTRAINT FK_9FAC415F32C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE project_content_imports DROP CONSTRAINT FK_9FAC415F93CB796C');
        $this->addSql('ALTER TABLE project_content_imports DROP CONSTRAINT FK_9FAC415FBE04EA9');
        $this->addSql('ALTER TABLE project_content_imports DROP CONSTRAINT FK_9FAC415F32C8A3DE');
        $this->addSql('DROP TABLE project_content_imports');
    }
}
