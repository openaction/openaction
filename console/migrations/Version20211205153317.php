<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211205153317 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_themes ADD default_colors JSON DEFAULT \'[]\'');
        $this->addSql('ALTER TABLE website_themes ALTER default_colors DROP DEFAULT');
        $this->addSql('ALTER TABLE website_themes ALTER default_colors SET NOT NULL');
        $this->addSql('ALTER TABLE website_themes ADD default_fonts JSON DEFAULT \'[]\'');
        $this->addSql('ALTER TABLE website_themes ALTER default_fonts DROP DEFAULT');
        $this->addSql('ALTER TABLE website_themes ALTER default_fonts SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_themes DROP default_colors');
        $this->addSql('ALTER TABLE website_themes DROP default_fonts');
    }
}
