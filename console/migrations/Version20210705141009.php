<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210705141009 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE analytics_community_contact_creations ADD has_phone BOOLEAN DEFAULT FALSE');
        $this->addSql('ALTER TABLE analytics_community_contact_creations ALTER has_phone DROP DEFAULT');
        $this->addSql('ALTER TABLE analytics_community_contact_creations ALTER has_phone SET NOT NULL');

        $this->addSql('ALTER TABLE analytics_community_contact_creations ADD receives_sms BOOLEAN DEFAULT FALSE');
        $this->addSql('ALTER TABLE analytics_community_contact_creations ALTER receives_sms DROP DEFAULT');
        $this->addSql('ALTER TABLE analytics_community_contact_creations ALTER receives_sms SET NOT NULL');

        $this->addSql('ALTER TABLE analytics_community_contact_creations ADD tags JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE analytics_community_contact_creations ADD country VARCHAR(2) DEFAULT NULL');
        $this->addSql('ALTER TABLE analytics_community_contact_creations ADD gender VARCHAR(20) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE analytics_community_contact_creations DROP has_phone');
        $this->addSql('ALTER TABLE analytics_community_contact_creations DROP receives_sms');
        $this->addSql('ALTER TABLE analytics_community_contact_creations DROP tags');
        $this->addSql('ALTER TABLE analytics_community_contact_creations DROP country');
        $this->addSql('ALTER TABLE analytics_community_contact_creations DROP gender');
    }
}
