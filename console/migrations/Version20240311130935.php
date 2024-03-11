<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240311130935 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_themes ADD posts_per_page SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE website_themes ADD events_per_page SMALLINT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_themes DROP posts_per_page');
        $this->addSql('ALTER TABLE website_themes DROP events_per_page');
    }
}
