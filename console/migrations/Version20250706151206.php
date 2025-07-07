<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250706151206 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations_members ADD projects_permissions_categories JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE registrations ADD projects_permissions_categories JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE registrations DROP projects_permissions_categories');
        $this->addSql('ALTER TABLE organizations_members DROP projects_permissions_categories');
    }
}
