<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220403153917 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_email_automations ADD unlayer_enabled BOOLEAN NOT NULL DEFAULT FALSE');
        $this->addSql('ALTER TABLE community_email_automations ALTER unlayer_enabled DROP DEFAULT');
        $this->addSql('ALTER TABLE community_email_automations ADD unlayer_design JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE community_emailing_campaigns ADD unlayer_enabled BOOLEAN NOT NULL DEFAULT FALSE');
        $this->addSql('ALTER TABLE community_emailing_campaigns ALTER unlayer_enabled DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_emailing_campaigns DROP unlayer_enabled');
        $this->addSql('ALTER TABLE community_email_automations DROP unlayer_enabled');
        $this->addSql('ALTER TABLE community_email_automations DROP unlayer_design');
    }
}
