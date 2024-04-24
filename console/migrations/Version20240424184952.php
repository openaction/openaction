<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240424184952 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_petitions_localized ADD legalities TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE website_petitions_localized ADD addressed_to VARCHAR(200) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_petitions_localized DROP legalities');
        $this->addSql('ALTER TABLE website_petitions_localized DROP addressed_to');
    }
}
