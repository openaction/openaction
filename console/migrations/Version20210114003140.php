<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210114003140 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_emailing_campaigns ADD tags_filter_type VARCHAR(10) DEFAULT \'or\'');
        $this->addSql('ALTER TABLE community_emailing_campaigns ALTER tags_filter_type DROP DEFAULT');
        $this->addSql('ALTER TABLE community_emailing_campaigns ALTER tags_filter_type SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_emailing_campaigns DROP tags_filter_type');
    }
}
