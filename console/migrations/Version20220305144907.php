<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220305144907 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_texting_campaigns ALTER content TYPE VARCHAR(500)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_texting_campaigns ALTER content TYPE VARCHAR(160)');
    }
}
