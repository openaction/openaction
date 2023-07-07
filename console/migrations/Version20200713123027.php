<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200713123027 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_contacts ADD metadata_flag1 BOOLEAN DEFAULT FALSE');
        $this->addSql('ALTER TABLE community_contacts ADD metadata_flag2 BOOLEAN DEFAULT FALSE');
        $this->addSql('ALTER TABLE community_contacts ADD metadata_flag3 BOOLEAN DEFAULT FALSE');
        $this->addSql('ALTER TABLE community_contacts ADD metadata_flag4 BOOLEAN DEFAULT FALSE');
        $this->addSql('ALTER TABLE community_contacts ADD metadata_flag5 BOOLEAN DEFAULT FALSE');
        $this->addSql('ALTER TABLE community_contacts ADD metadata_flag6 BOOLEAN DEFAULT FALSE');
        $this->addSql('ALTER TABLE community_contacts ADD metadata_flag7 BOOLEAN DEFAULT FALSE');
        $this->addSql('ALTER TABLE community_contacts ADD metadata_flag8 BOOLEAN DEFAULT FALSE');
        $this->addSql('ALTER TABLE community_contacts ALTER metadata_flag1 DROP DEFAULT');
        $this->addSql('ALTER TABLE community_contacts ALTER metadata_flag1 SET NOT NULL');
        $this->addSql('ALTER TABLE community_contacts ALTER metadata_flag2 DROP DEFAULT');
        $this->addSql('ALTER TABLE community_contacts ALTER metadata_flag2 SET NOT NULL');
        $this->addSql('ALTER TABLE community_contacts ALTER metadata_flag3 DROP DEFAULT');
        $this->addSql('ALTER TABLE community_contacts ALTER metadata_flag3 SET NOT NULL');
        $this->addSql('ALTER TABLE community_contacts ALTER metadata_flag4 DROP DEFAULT');
        $this->addSql('ALTER TABLE community_contacts ALTER metadata_flag4 SET NOT NULL');
        $this->addSql('ALTER TABLE community_contacts ALTER metadata_flag5 DROP DEFAULT');
        $this->addSql('ALTER TABLE community_contacts ALTER metadata_flag5 SET NOT NULL');
        $this->addSql('ALTER TABLE community_contacts ALTER metadata_flag6 DROP DEFAULT');
        $this->addSql('ALTER TABLE community_contacts ALTER metadata_flag6 SET NOT NULL');
        $this->addSql('ALTER TABLE community_contacts ALTER metadata_flag7 DROP DEFAULT');
        $this->addSql('ALTER TABLE community_contacts ALTER metadata_flag7 SET NOT NULL');
        $this->addSql('ALTER TABLE community_contacts ALTER metadata_flag8 DROP DEFAULT');
        $this->addSql('ALTER TABLE community_contacts ALTER metadata_flag8 SET NOT NULL');
        $this->addSql('ALTER TABLE community_contacts DROP metadata_flags');
        $this->addSql('ALTER TABLE organizations ADD contacts_flags_label1 VARCHAR(30) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD contacts_flags_label2 VARCHAR(30) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD contacts_flags_label3 VARCHAR(30) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD contacts_flags_label4 VARCHAR(30) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD contacts_flags_label5 VARCHAR(30) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD contacts_flags_label6 VARCHAR(30) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD contacts_flags_label7 VARCHAR(30) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD contacts_flags_label8 VARCHAR(30) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations DROP contacts_flags_labels');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations ADD contacts_flags_labels JSON NOT NULL');
        $this->addSql('ALTER TABLE organizations DROP contacts_flags_label1');
        $this->addSql('ALTER TABLE organizations DROP contacts_flags_label2');
        $this->addSql('ALTER TABLE organizations DROP contacts_flags_label3');
        $this->addSql('ALTER TABLE organizations DROP contacts_flags_label4');
        $this->addSql('ALTER TABLE organizations DROP contacts_flags_label5');
        $this->addSql('ALTER TABLE organizations DROP contacts_flags_label6');
        $this->addSql('ALTER TABLE organizations DROP contacts_flags_label7');
        $this->addSql('ALTER TABLE organizations DROP contacts_flags_label8');
        $this->addSql('ALTER TABLE community_contacts ADD metadata_flags JSON NOT NULL');
        $this->addSql('ALTER TABLE community_contacts DROP metadata_flag1');
        $this->addSql('ALTER TABLE community_contacts DROP metadata_flag2');
        $this->addSql('ALTER TABLE community_contacts DROP metadata_flag3');
        $this->addSql('ALTER TABLE community_contacts DROP metadata_flag4');
        $this->addSql('ALTER TABLE community_contacts DROP metadata_flag5');
        $this->addSql('ALTER TABLE community_contacts DROP metadata_flag6');
        $this->addSql('ALTER TABLE community_contacts DROP metadata_flag7');
        $this->addSql('ALTER TABLE community_contacts DROP metadata_flag8');
    }
}
