<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220201173703 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_printing_campaigns ADD delivery_main_address_instructions TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE community_printing_campaigns ADD recipient_first_name VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_printing_campaigns ADD recipient_last_name VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_printing_campaigns ADD recipient_email VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_printing_campaigns ADD recipient_phone VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_printing_campaigns DROP delivery_main_phone');
        $this->addSql('ALTER TABLE organizations ADD print_delivery_area VARCHAR(150) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations DROP print_delivery_area');
        $this->addSql('ALTER TABLE community_printing_campaigns ADD delivery_main_phone VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_printing_campaigns DROP delivery_main_address_instructions');
        $this->addSql('ALTER TABLE community_printing_campaigns DROP recipient_first_name');
        $this->addSql('ALTER TABLE community_printing_campaigns DROP recipient_last_name');
        $this->addSql('ALTER TABLE community_printing_campaigns DROP recipient_email');
        $this->addSql('ALTER TABLE community_printing_campaigns DROP recipient_phone');
    }
}
