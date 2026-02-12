<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260212220000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add campaign-level global stats columns on community_emailing_campaigns';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_emailing_campaigns ADD global_stats_sent INT DEFAULT NULL');
        $this->addSql('ALTER TABLE community_emailing_campaigns ADD global_stats_opened INT DEFAULT NULL');
        $this->addSql('ALTER TABLE community_emailing_campaigns ADD global_stats_clicked INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_emailing_campaigns DROP global_stats_sent');
        $this->addSql('ALTER TABLE community_emailing_campaigns DROP global_stats_opened');
        $this->addSql('ALTER TABLE community_emailing_campaigns DROP global_stats_clicked');
    }
}
