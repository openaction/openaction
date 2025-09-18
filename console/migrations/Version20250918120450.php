<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250918120450 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_petitions_localized ALTER description TYPE TEXT');
        $this->addSql('ALTER TABLE website_petitions_localized ALTER description TYPE TEXT');
        $this->addSql('ALTER TABLE website_petitions_localized ALTER submit_button_label TYPE VARCHAR(200)');
        $this->addSql('ALTER TABLE website_petitions_localized ALTER optin_label TYPE TEXT');
        $this->addSql('ALTER TABLE website_petitions_localized ALTER optin_label TYPE TEXT');
        $this->addSql('ALTER TABLE website_petitions_localized ALTER addressed_to TYPE TEXT');
        $this->addSql('ALTER TABLE website_petitions_localized ALTER addressed_to TYPE TEXT');
        $this->addSql('DROP INDEX idx_3c9c8fef5ff69b7d');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3C9C8FEF5FF69B7D ON website_petitions_localized (form_id)');
    }
}
