<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220407132719 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_printing_campaigns ADD preview_address_name VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_printing_campaigns ADD preview_address_street1 VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_printing_campaigns ADD preview_address_street2 VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_printing_campaigns ADD preview_address_zip_code VARCHAR(10) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_printing_campaigns ADD preview_address_city VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_printing_campaigns ADD preview_address_country VARCHAR(2) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_printing_campaigns ADD preview_address_instructions TEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_printing_campaigns DROP preview_address_name');
        $this->addSql('ALTER TABLE community_printing_campaigns DROP preview_address_street1');
        $this->addSql('ALTER TABLE community_printing_campaigns DROP preview_address_street2');
        $this->addSql('ALTER TABLE community_printing_campaigns DROP preview_address_zip_code');
        $this->addSql('ALTER TABLE community_printing_campaigns DROP preview_address_city');
        $this->addSql('ALTER TABLE community_printing_campaigns DROP preview_address_country');
        $this->addSql('ALTER TABLE community_printing_campaigns DROP preview_address_instructions');
    }
}
