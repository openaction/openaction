<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210726173936 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE domains ADD configuration_status JSON DEFAULT \'["cloudflare_ready", "sendgrid_ready", "postmark_ready"]\'');
        $this->addSql('ALTER TABLE domains ALTER configuration_status DROP DEFAULT');
        $this->addSql('ALTER TABLE domains ALTER configuration_status SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE domains DROP configuration_status');
    }
}
