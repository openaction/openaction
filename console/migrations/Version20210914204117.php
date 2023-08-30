<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210914204117 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE projects_tags (project_id BIGINT NOT NULL, tag_id BIGINT NOT NULL, PRIMARY KEY(project_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_51A228EE166D1F9C ON projects_tags (project_id)');
        $this->addSql('CREATE INDEX IDX_51A228EEBAD26311 ON projects_tags (tag_id)');
        $this->addSql('ALTER TABLE projects_tags ADD CONSTRAINT FK_51A228EE166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE projects_tags ADD CONSTRAINT FK_51A228EEBAD26311 FOREIGN KEY (tag_id) REFERENCES community_tags (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE projects_tags');
    }
}
