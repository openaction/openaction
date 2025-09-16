<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250916101500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename join column to match ORM mapping for petitions localized categories link table';
    }

    public function up(Schema $schema): void
    {
        // Drop FK and index using old column name, then rename column, recreate index and FK
        $this->addSql('ALTER TABLE website_petitions_localized_petitions_localized_categories DROP CONSTRAINT IF EXISTS FK_73F58100BF5C489E');
        $this->addSql('DROP INDEX IF EXISTS IDX_73F58100BF5C489E');
        $this->addSql('ALTER TABLE website_petitions_localized_petitions_localized_categories RENAME COLUMN petition_localized_id TO localized_petition_id');
        $this->addSql('CREATE INDEX IDX_73F58100BF5C489E ON website_petitions_localized_petitions_localized_categories (localized_petition_id)');
        $this->addSql('ALTER TABLE website_petitions_localized_petitions_localized_categories ADD CONSTRAINT FK_73F58100BF5C489E FOREIGN KEY (localized_petition_id) REFERENCES website_petitions_localized (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_petitions_localized_petitions_localized_categories DROP CONSTRAINT IF EXISTS FK_73F58100BF5C489E');
        $this->addSql('DROP INDEX IF EXISTS IDX_73F58100BF5C489E');
        $this->addSql('ALTER TABLE website_petitions_localized_petitions_localized_categories RENAME COLUMN localized_petition_id TO petition_localized_id');
        $this->addSql('CREATE INDEX IDX_73F58100BF5C489E ON website_petitions_localized_petitions_localized_categories (petition_localized_id)');
        $this->addSql('ALTER TABLE website_petitions_localized_petitions_localized_categories ADD CONSTRAINT FK_73F58100BF5C489E FOREIGN KEY (petition_localized_id) REFERENCES website_petitions_localized (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}

