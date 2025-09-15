<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250915120000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations ADD mollie_connect_access_token_expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    }
}
