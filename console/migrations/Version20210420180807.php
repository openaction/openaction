<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210420180807 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_contacts DROP metadata_flag1');
        $this->addSql('ALTER TABLE community_contacts DROP metadata_flag2');
        $this->addSql('ALTER TABLE community_contacts DROP metadata_flag3');
        $this->addSql('ALTER TABLE community_contacts DROP metadata_flag4');
        $this->addSql('ALTER TABLE community_contacts DROP metadata_flag5');
        $this->addSql('ALTER TABLE community_contacts DROP metadata_flag6');
        $this->addSql('ALTER TABLE community_contacts DROP metadata_flag7');
        $this->addSql('ALTER TABLE community_contacts DROP metadata_flag8');
        $this->addSql('ALTER TABLE organizations DROP contacts_flags_label1');
        $this->addSql('ALTER TABLE organizations DROP contacts_flags_label2');
        $this->addSql('ALTER TABLE organizations DROP contacts_flags_label3');
        $this->addSql('ALTER TABLE organizations DROP contacts_flags_label4');
        $this->addSql('ALTER TABLE organizations DROP contacts_flags_label5');
        $this->addSql('ALTER TABLE organizations DROP contacts_flags_label6');
        $this->addSql('ALTER TABLE organizations DROP contacts_flags_label7');
        $this->addSql('ALTER TABLE organizations DROP contacts_flags_label8');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_contacts ADD metadata_flag1 BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD metadata_flag2 BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD metadata_flag3 BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD metadata_flag4 BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD metadata_flag5 BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD metadata_flag6 BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD metadata_flag7 BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD metadata_flag8 BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE organizations ADD contacts_flags_label1 VARCHAR(30) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD contacts_flags_label2 VARCHAR(30) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD contacts_flags_label3 VARCHAR(30) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD contacts_flags_label4 VARCHAR(30) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD contacts_flags_label5 VARCHAR(30) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD contacts_flags_label6 VARCHAR(30) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD contacts_flags_label7 VARCHAR(30) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD contacts_flags_label8 VARCHAR(30) DEFAULT NULL');
    }
}
