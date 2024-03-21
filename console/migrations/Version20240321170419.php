<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240321170419 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_manifestos_topics ALTER description TYPE TEXT');
        $this->addSql('ALTER TABLE website_pages ALTER description TYPE TEXT');
        $this->addSql('ALTER TABLE website_posts ALTER description TYPE TEXT');
        $this->addSql('ALTER TABLE website_trombinoscope_persons ALTER description TYPE TEXT');
    }

    public function down(Schema $schema): void
    {
    }
}
