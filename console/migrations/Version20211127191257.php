<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211127191257 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE themes_assets RENAME TO projects_assets');
        $this->addSql('ALTER SEQUENCE themes_assets_id_seq RENAME TO projects_assets_id_seq');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects_assets RENAME TO projects_assets');
        $this->addSql('ALTER SEQUENCE projects_assets_id_seq RENAME TO themes_assets_id_seq');
    }
}
