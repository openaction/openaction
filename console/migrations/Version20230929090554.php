<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230929090554 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_pages ADD parent_id BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE website_pages ADD CONSTRAINT FK_771E55B9727ACA70 FOREIGN KEY (parent_id) REFERENCES website_pages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_771E55B9727ACA70 ON website_pages (parent_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_pages DROP CONSTRAINT FK_771E55B9727ACA70');
        $this->addSql('DROP INDEX IDX_771E55B9727ACA70');
        $this->addSql('ALTER TABLE website_pages DROP parent_id');
    }
}
