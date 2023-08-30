<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220413141256 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_printing_orders ADD recipient_candidate VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_printing_orders ALTER recipient_department TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE community_printing_orders ALTER recipient_circonscription TYPE VARCHAR(10)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_printing_orders DROP recipient_candidate');
        $this->addSql('ALTER TABLE community_printing_orders ALTER recipient_department TYPE VARCHAR(200)');
        $this->addSql('ALTER TABLE community_printing_orders ALTER recipient_circonscription TYPE VARCHAR(200)');
    }
}
