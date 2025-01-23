<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250123210105 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_trombinoscope_persons ADD social_bluesky VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE website_trombinoscope_persons ADD social_mastodon VARCHAR(150) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_trombinoscope_persons DROP social_bluesky');
        $this->addSql('ALTER TABLE website_trombinoscope_persons DROP social_mastodon');
    }
}
