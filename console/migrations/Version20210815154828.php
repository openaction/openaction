<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210815154828 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_imports DROP delimiter');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_imports ADD delimiter VARCHAR(3) NOT NULL');
    }
}
