<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201124231514 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_emailing_campaigns ADD reply_to_email VARCHAR(250) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_emailing_campaigns ADD reply_to_name VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_email_automations ADD reply_to_email VARCHAR(250) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_email_automations ADD reply_to_name VARCHAR(150) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_emailing_campaigns DROP reply_to_email');
        $this->addSql('ALTER TABLE community_emailing_campaigns DROP reply_to_name');
        $this->addSql('ALTER TABLE community_email_automations DROP reply_to_email');
        $this->addSql('ALTER TABLE community_email_automations DROP reply_to_name');
    }
}
