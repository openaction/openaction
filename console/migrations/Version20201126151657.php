<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201126151657 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_contacts ADD parsed_contact_phone VARCHAR(35) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD parsed_contact_work_phone VARCHAR(35) DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN community_contacts.parsed_contact_phone IS \'(DC2Type:phone_number)\'');
        $this->addSql('COMMENT ON COLUMN community_contacts.parsed_contact_work_phone IS \'(DC2Type:phone_number)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_contacts DROP parsed_contact_phone');
        $this->addSql('ALTER TABLE community_contacts DROP parsed_contact_work_phone');
    }
}
