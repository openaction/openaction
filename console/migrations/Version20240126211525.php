<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240126211525 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects ADD website_turnstile_site_key VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE projects ADD website_turnstile_secret_key VARCHAR(64) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects DROP website_turnstile_site_key');
        $this->addSql('ALTER TABLE projects DROP website_turnstile_secret_key');
    }
}
