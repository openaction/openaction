<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200919091552 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_contacts ADD settings_receive_sms BOOLEAN DEFAULT TRUE');
        $this->addSql('ALTER TABLE community_contacts ALTER settings_receive_sms DROP DEFAULT');
        $this->addSql('ALTER TABLE community_contacts ALTER settings_receive_sms SET NOT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD settings_receive_calls BOOLEAN DEFAULT FALSE');
        $this->addSql('ALTER TABLE community_contacts ALTER settings_receive_calls DROP DEFAULT');
        $this->addSql('ALTER TABLE community_contacts ALTER settings_receive_calls SET NOT NULL');
        $this->addSql('ALTER TABLE community_contacts DROP settings_receive_events');
        $this->addSql('ALTER TABLE community_contacts DROP settings_unsubscribe_all');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_contacts ADD settings_receive_events BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD settings_unsubscribe_all BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE community_contacts DROP settings_receive_sms');
        $this->addSql('ALTER TABLE community_contacts DROP settings_receive_calls');
    }
}
