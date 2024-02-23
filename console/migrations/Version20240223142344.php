<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240223142344 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects_content_imports DROP CONSTRAINT fk_dc784a8132c8a3de');
        $this->addSql('DROP INDEX idx_dc784a8132c8a3de');
        $this->addSql('ALTER TABLE projects_content_imports RENAME COLUMN organization_id TO project_id');
        $this->addSql('ALTER TABLE projects_content_imports ADD CONSTRAINT FK_DC784A81166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_DC784A81166D1F9C ON projects_content_imports (project_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects_content_imports DROP CONSTRAINT FK_DC784A81166D1F9C');
        $this->addSql('DROP INDEX IDX_DC784A81166D1F9C');
        $this->addSql('ALTER TABLE projects_content_imports RENAME COLUMN project_id TO organization_id');
        $this->addSql('ALTER TABLE projects_content_imports ADD CONSTRAINT fk_dc784a8132c8a3de FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_dc784a8132c8a3de ON projects_content_imports (organization_id)');
    }
}
