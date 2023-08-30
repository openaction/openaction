<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210505200228 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_trombinoscope_persons ALTER role TYPE VARCHAR(250)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_trombinoscope_persons ALTER role TYPE VARCHAR(100)');
    }
}
