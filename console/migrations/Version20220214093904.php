<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220214093904 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects ADD website_animate_elements BOOLEAN NOT NULL DEFAULT FALSE');
        $this->addSql('ALTER TABLE projects ALTER website_animate_elements DROP DEFAULT');
        $this->addSql('ALTER TABLE projects ADD website_animate_links BOOLEAN NOT NULL DEFAULT FALSE');
        $this->addSql('ALTER TABLE projects ALTER website_animate_links DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects DROP website_animate_elements');
        $this->addSql('ALTER TABLE projects DROP website_animate_links');
    }
}
