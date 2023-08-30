<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200914193940 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE users_visits_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE users_visits (id BIGINT NOT NULL, owner_id BIGINT NOT NULL, date DATE NOT NULL, page_views BIGINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX users_visits_owner ON users_visits (owner_id)');
        $this->addSql('CREATE INDEX users_visits_date ON users_visits (date)');
        $this->addSql('ALTER TABLE users_visits ADD CONSTRAINT FK_4BAFB77A7E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE users_visits_id_seq CASCADE');
        $this->addSql('DROP TABLE users_visits');
    }
}
