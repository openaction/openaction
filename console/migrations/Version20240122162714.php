<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240122162714 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects ADD social_phone VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE projects ADD social_whatsapp VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE projects ADD social_tiktok VARCHAR(150) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects DROP social_phone');
        $this->addSql('ALTER TABLE projects DROP social_whatsapp');
        $this->addSql('ALTER TABLE projects DROP social_tiktok');
    }
}
