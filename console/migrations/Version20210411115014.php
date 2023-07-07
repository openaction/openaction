<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210411115014 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE domains ADD managed_automatically BOOLEAN DEFAULT TRUE');
        $this->addSql('ALTER TABLE domains ALTER managed_automatically DROP DEFAULT');
        $this->addSql('ALTER TABLE domains ALTER managed_automatically SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE domains DROP managed_automatically');
    }
}
