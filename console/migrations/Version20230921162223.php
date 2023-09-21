<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230921162223 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_events ADD external_url VARCHAR(250) DEFAULT NULL');
        $this->addSql('ALTER TABLE website_posts ADD external_url VARCHAR(250) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_events DROP external_url');
        $this->addSql('ALTER TABLE website_posts DROP external_url');
    }
}
