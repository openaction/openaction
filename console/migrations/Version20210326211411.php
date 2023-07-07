<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210326211411 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE domains ADD cloudflare_config JSON DEFAULT \'{}\'');
        $this->addSql('ALTER TABLE domains ALTER cloudflare_config DROP DEFAULT');
        $this->addSql('ALTER TABLE domains ALTER cloudflare_config SET NOT NULL');

        $this->addSql('ALTER TABLE domains ADD sendgrid_config JSON DEFAULT \'{}\'');
        $this->addSql('ALTER TABLE domains ALTER sendgrid_config DROP DEFAULT');
        $this->addSql('ALTER TABLE domains ALTER sendgrid_config SET NOT NULL');

        $this->addSql('ALTER TABLE domains ADD postmark_config JSON DEFAULT \'{}\'');
        $this->addSql('ALTER TABLE domains ALTER postmark_config DROP DEFAULT');
        $this->addSql('ALTER TABLE domains ALTER postmark_config SET NOT NULL');

        $this->addSql('
            UPDATE domains d SET cloudflare_config = (
                SELECT row_to_json(r)
                FROM (SELECT sd.zone_id AS id, sd.name, \'active\' AS status FROM domains sd WHERE d.name = sd.name LIMIT 1) r
            )
        ');

        $this->addSql('ALTER TABLE domains DROP zone_id');
        $this->addSql('ALTER TABLE domains DROP status');
        $this->addSql('ALTER TABLE domains DROP config');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE domains ADD zone_id VARCHAR(75) DEFAULT NULL');
        $this->addSql('ALTER TABLE domains ADD status VARCHAR(30) NOT NULL');
        $this->addSql('ALTER TABLE domains ADD config JSON NOT NULL');
        $this->addSql('ALTER TABLE domains DROP cloudflare_config');
        $this->addSql('ALTER TABLE domains DROP sendgrid_config');
        $this->addSql('ALTER TABLE domains DROP postmark_config');
    }
}
