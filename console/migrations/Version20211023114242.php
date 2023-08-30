<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211023114242 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_contacts ADD picture_id BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD CONSTRAINT FK_C106D054EE45BDBF FOREIGN KEY (picture_id) REFERENCES uploads (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C106D054EE45BDBF ON community_contacts (picture_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_contacts DROP CONSTRAINT FK_C106D054EE45BDBF');
        $this->addSql('DROP INDEX UNIQ_C106D054EE45BDBF');
        $this->addSql('ALTER TABLE community_contacts DROP picture_id');
    }
}
