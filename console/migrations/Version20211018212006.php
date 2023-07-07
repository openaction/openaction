<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211018212006 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_phoning_campaigns_calls ALTER status TYPE VARCHAR(25)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_phoning_campaigns_calls ALTER status TYPE VARCHAR(15)');
    }
}
