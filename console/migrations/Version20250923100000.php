<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250923100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Drop deprecated Spallian endpoint column';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations DROP spallian_endpoint');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations ADD spallian_endpoint VARCHAR(200) DEFAULT NULL');
    }
}
