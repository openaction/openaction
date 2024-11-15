<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241115141129 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE UNIQUE INDEX community_emailing_campaigns_messages_unique_idx ON community_emailing_campaigns_messages (contact_id, campaign_id)');
        $this->addSql('CREATE UNIQUE INDEX community_texting_campaigns_messages_unique_idx ON community_texting_campaigns_messages (contact_id, campaign_id)');
    }

    public function down(Schema $schema): void
    {
    }
}
