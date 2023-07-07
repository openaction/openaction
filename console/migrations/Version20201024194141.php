<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201024194141 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE community_email_automations SET trigger = \'new_contact\' WHERE trigger = \'contact_created\'');
        $this->addSql('ALTER TABLE community_contacts ADD account_confirmed BOOLEAN DEFAULT FALSE');
        $this->addSql('ALTER TABLE community_contacts ALTER account_confirmed DROP DEFAULT');
        $this->addSql('ALTER TABLE community_contacts ALTER account_confirmed SET NOT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD account_reset_requested_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD account_reset_token VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_contacts DROP internal_last_logged_at');
        $this->addSql('ALTER TABLE community_contacts DROP internal_email_confirmed');
        $this->addSql('ALTER TABLE community_contacts DROP internal_email_bounced');
        $this->addSql('ALTER TABLE community_contacts DROP internal_registration_uuid');
        $this->addSql('ALTER TABLE community_contacts RENAME COLUMN internal_token TO account_confirm_token');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_contacts ADD internal_last_logged_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD internal_email_bounced BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD internal_token VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD internal_registration_uuid UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE community_contacts DROP account_confirm_token');
        $this->addSql('ALTER TABLE community_contacts DROP account_reset_requested_at');
        $this->addSql('ALTER TABLE community_contacts DROP account_reset_token');
        $this->addSql('ALTER TABLE community_contacts RENAME COLUMN account_confirmed TO internal_email_confirmed');
        $this->addSql('COMMENT ON COLUMN community_contacts.internal_registration_uuid IS \'(DC2Type:uuid)\'');
    }
}
