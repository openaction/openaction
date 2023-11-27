<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231127203832 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_events ADD timezone VARCHAR(100) DEFAULT NULL');
        $this->addSql('UPDATE website_events SET timezone = \'Europe/Paris\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_events DROP timezone');
    }
}
