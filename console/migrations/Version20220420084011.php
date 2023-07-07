<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220420084011 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_printing_campaigns ADD source_error JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE community_printing_orders ADD delivery_main_address_provider VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_printing_orders ADD delivery_main_address_tracking_code VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_printing_orders ADD delivery_poster_address_name VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_printing_orders ADD delivery_poster_address_street1 VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_printing_orders ADD delivery_poster_address_street2 VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_printing_orders ADD delivery_poster_address_provider VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_printing_orders ADD delivery_poster_address_tracking_code VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_printing_orders DROP preview_address_name');
        $this->addSql('ALTER TABLE community_printing_orders DROP preview_address_street1');
        $this->addSql('ALTER TABLE community_printing_orders DROP preview_address_street2');
        $this->addSql('ALTER TABLE community_printing_orders RENAME COLUMN preview_address_zip_code TO delivery_poster_address_zip_code');
        $this->addSql('ALTER TABLE community_printing_orders RENAME COLUMN preview_address_city TO delivery_poster_address_city');
        $this->addSql('ALTER TABLE community_printing_orders RENAME COLUMN preview_address_country TO delivery_poster_address_country');
        $this->addSql('ALTER TABLE community_printing_orders RENAME COLUMN preview_address_instructions TO delivery_poster_address_instructions');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_printing_campaigns DROP source_error');
        $this->addSql('ALTER TABLE community_printing_orders ADD preview_address_name VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_printing_orders ADD preview_address_street1 VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_printing_orders ADD preview_address_street2 VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_printing_orders DROP delivery_main_address_provider');
        $this->addSql('ALTER TABLE community_printing_orders DROP delivery_main_address_tracking_code');
        $this->addSql('ALTER TABLE community_printing_orders DROP delivery_poster_address_name');
        $this->addSql('ALTER TABLE community_printing_orders DROP delivery_poster_address_street1');
        $this->addSql('ALTER TABLE community_printing_orders DROP delivery_poster_address_street2');
        $this->addSql('ALTER TABLE community_printing_orders DROP delivery_poster_address_provider');
        $this->addSql('ALTER TABLE community_printing_orders DROP delivery_poster_address_tracking_code');
        $this->addSql('ALTER TABLE community_printing_orders RENAME COLUMN delivery_poster_address_zip_code TO preview_address_zip_code');
        $this->addSql('ALTER TABLE community_printing_orders RENAME COLUMN delivery_poster_address_city TO preview_address_city');
        $this->addSql('ALTER TABLE community_printing_orders RENAME COLUMN delivery_poster_address_country TO preview_address_country');
        $this->addSql('ALTER TABLE community_printing_orders RENAME COLUMN delivery_poster_address_instructions TO preview_address_instructions');
    }
}
