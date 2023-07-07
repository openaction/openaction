<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210607213023 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_tags ALTER name TYPE VARCHAR(150)');
        $this->addSql('ALTER TABLE community_tags ALTER slug TYPE VARCHAR(150)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_tags ALTER name TYPE VARCHAR(50)');
        $this->addSql('ALTER TABLE community_tags ALTER slug TYPE VARCHAR(50)');
    }
}
