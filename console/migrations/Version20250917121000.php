<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250917121000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add unique constraint on website_petitions (project_id, slug)';
    }

    public function up(Schema $schema): void
    {
        // Ensure unique slug per project for petitions
        $this->addSql('CREATE UNIQUE INDEX website_petitions_project_slug_unique ON website_petitions (project_id, slug)');
    }
}
