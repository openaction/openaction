<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231001101939 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations ADD white_label_logo_id BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD white_label_name VARCHAR(25) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD CONSTRAINT FK_427C1C7F4B3AD6E1 FOREIGN KEY (white_label_logo_id) REFERENCES uploads (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_427C1C7F4B3AD6E1 ON organizations (white_label_logo_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations DROP CONSTRAINT FK_427C1C7F4B3AD6E1');
        $this->addSql('DROP INDEX UNIQ_427C1C7F4B3AD6E1');
        $this->addSql('ALTER TABLE organizations DROP white_label_logo_id');
        $this->addSql('ALTER TABLE organizations DROP white_label_name');
    }
}
