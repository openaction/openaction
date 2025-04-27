<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250427124346 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_emailing_campaigns_messages ADD unsubscribed BOOLEAN NOT NULL DEFAULT FALSE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_emailing_campaigns_messages DROP unsubscribed');
    }
}
