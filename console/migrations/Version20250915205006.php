<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250915205006 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_phoning_campaigns DROP CONSTRAINT FK_61217EB95FF69B7D');
        $this->addSql('ALTER TABLE community_phoning_campaigns ADD CONSTRAINT FK_61217EB95FF69B7D FOREIGN KEY (form_id) REFERENCES website_forms (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
