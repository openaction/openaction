<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210109213021 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE analytics_website_sessions ADD organization_id BIGINT DEFAULT NULL');
        $this->addSql('UPDATE analytics_website_sessions SET organization_id = (SELECT p.organization_id FROM projects p WHERE p.id = project_id)');
        $this->addSql('ALTER TABLE analytics_website_sessions ALTER organization_id DROP DEFAULT');
        $this->addSql('ALTER TABLE analytics_website_sessions ALTER organization_id SET NOT NULL');
        $this->addSql('ALTER TABLE analytics_website_sessions ADD CONSTRAINT FK_7964ACCC32C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_7964ACCC32C8A3DE ON analytics_website_sessions (organization_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE analytics_website_sessions DROP CONSTRAINT FK_7964ACCC32C8A3DE');
        $this->addSql('DROP INDEX IDX_7964ACCC32C8A3DE');
        $this->addSql('ALTER TABLE analytics_website_sessions DROP organization_id');
    }
}
