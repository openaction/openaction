<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250404102306 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations ADD email_enable_open_tracking BOOLEAN NOT NULL DEFAULT TRUE');
        $this->addSql('ALTER TABLE organizations ADD email_enable_click_tracking BOOLEAN NOT NULL DEFAULT TRUE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations DROP email_enable_open_tracking');
        $this->addSql('ALTER TABLE organizations DROP email_enable_click_tracking');
    }
}
