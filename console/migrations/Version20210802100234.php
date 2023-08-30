<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210802100234 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations ADD show_preview BOOLEAN DEFAULT FALSE');
        $this->addSql('ALTER TABLE organizations ALTER show_preview DROP DEFAULT');
        $this->addSql('ALTER TABLE organizations ALTER show_preview SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations DROP show_preview');
    }
}
