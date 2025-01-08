<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250108125654 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX community_emailing_campaigns_messages_logs_type ON community_emailing_campaigns_messages_logs (type)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX community_emailing_campaigns_messages_logs_type');
    }
}
