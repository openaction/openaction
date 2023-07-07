<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220529095453 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_contacts ADD settings_by_project JSONB NOT NULL DEFAULT \'{}\'');
        $this->addSql('ALTER TABLE community_contacts ALTER settings_by_project DROP DEFAULT');
        $this->addSql('CREATE INDEX community_contacts_settings_by_projects_idx ON community_contacts USING gin (settings_by_project jsonb_path_ops);');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_contacts DROP settings_by_project');
        $this->addSql('ALTER TABLE community_contacts DROP INDEX  community_contacts_settings_by_projects_idx');
    }
}
