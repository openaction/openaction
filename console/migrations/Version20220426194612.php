<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220426194612 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations ALTER print_subrogated DROP DEFAULT');
        $this->addSql('ALTER TABLE organizations RENAME COLUMN print_delivery_area TO print_party');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations ALTER print_subrogated SET DEFAULT \'false\'');
        $this->addSql('ALTER TABLE organizations RENAME COLUMN print_party TO print_delivery_area');
    }
}
