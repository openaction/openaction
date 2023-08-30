<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220530074248 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX users_visits_owner_date ON users_visits (owner_id, date)');
        $this->addSql('ALTER TABLE website_menu_items DROP CONSTRAINT FK_F19F040F727ACA70');
        $this->addSql('ALTER TABLE website_menu_items ADD CONSTRAINT FK_F19F040F727ACA70 FOREIGN KEY (parent_id) REFERENCES website_menu_items (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_menu_items DROP CONSTRAINT fk_f19f040f727aca70');
        $this->addSql('ALTER TABLE website_menu_items ADD CONSTRAINT fk_f19f040f727aca70 FOREIGN KEY (parent_id) REFERENCES website_menu_items (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP INDEX users_visits_owner_date');
    }
}
