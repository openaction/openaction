<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211206111734 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_events ADD form_id BIGINT NULL');
        $this->addSql('ALTER TABLE website_events ADD CONSTRAINT FK_C102B3615FF69B7D FOREIGN KEY (form_id) REFERENCES website_forms (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_C102B3615FF69B7D ON website_events (form_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_events DROP CONSTRAINT FK_C102B3615FF69B7D');
        $this->addSql('DROP INDEX IDX_C102B3615FF69B7D');
        $this->addSql('ALTER TABLE website_events DROP form_id');
    }
}
