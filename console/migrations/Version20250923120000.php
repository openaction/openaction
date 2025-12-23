<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250923120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create shared PDO cache and session tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS cache_items (item_id VARCHAR(255) NOT NULL PRIMARY KEY, item_data BYTEA NOT NULL, item_lifetime INTEGER DEFAULT NULL, item_time INTEGER DEFAULT 0 NOT NULL)');
        $this->addSql('CREATE INDEX IF NOT EXISTS cache_items_lifetime ON cache_items (item_lifetime)');
        $this->addSql('CREATE INDEX IF NOT EXISTS cache_items_time ON cache_items (item_time)');

        $this->addSql('CREATE TABLE IF NOT EXISTS sessions (sess_id VARCHAR(128) NOT NULL PRIMARY KEY, sess_data BYTEA NOT NULL, sess_lifetime INTEGER NOT NULL, sess_time INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX IF NOT EXISTS sessions_lifetime ON sessions (sess_lifetime)');
        $this->addSql('CREATE INDEX IF NOT EXISTS sessions_time ON sessions (sess_time)');
    }
}
