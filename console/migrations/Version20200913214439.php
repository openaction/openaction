<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200913214439 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE announcements_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE announcements (id BIGINT NOT NULL, title VARCHAR(100) NOT NULL, description VARCHAR(250) NOT NULL, link_text VARCHAR(50) DEFAULT NULL, link_url VARCHAR(200) DEFAULT NULL, locale VARCHAR(10) NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE announcements_id_seq CASCADE');
        $this->addSql('DROP TABLE announcements');
    }
}
