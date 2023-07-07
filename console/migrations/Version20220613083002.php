<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220613083002 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations ADD crm_search_key TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations RENAME COLUMN crm_index TO crm_index_version');
        $this->addSql('ALTER TABLE organizations_members ADD crm_tenant_token TEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations_members DROP crm_tenant_token');
        $this->addSql('ALTER TABLE organizations DROP crm_search_key');
        $this->addSql('ALTER TABLE organizations RENAME COLUMN crm_index_version TO crm_index');
    }
}
