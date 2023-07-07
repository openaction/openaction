<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200902213543 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_events DROP description');
        $this->addSql('ALTER TABLE website_events DROP quote');
        $this->addSql('ALTER TABLE website_events ALTER title TYPE VARCHAR(200)');
        $this->addSql('ALTER TABLE website_events ALTER slug TYPE VARCHAR(200)');
        $this->addSql('ALTER TABLE website_pages ALTER title TYPE VARCHAR(200)');
        $this->addSql('ALTER TABLE website_pages ALTER slug TYPE VARCHAR(200)');
        $this->addSql('ALTER TABLE website_pages ALTER description TYPE VARCHAR(200)');
        $this->addSql('ALTER TABLE website_posts ALTER title TYPE VARCHAR(200)');
        $this->addSql('ALTER TABLE website_posts ALTER slug TYPE VARCHAR(200)');
        $this->addSql('ALTER TABLE website_posts ALTER description TYPE VARCHAR(200)');
        $this->addSql('ALTER TABLE website_posts ALTER quote TYPE VARCHAR(200)');
        $this->addSql('ALTER TABLE website_posts ALTER video TYPE VARCHAR(50)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_posts ALTER title TYPE VARCHAR(100)');
        $this->addSql('ALTER TABLE website_posts ALTER slug TYPE VARCHAR(100)');
        $this->addSql('ALTER TABLE website_posts ALTER description TYPE VARCHAR(150)');
        $this->addSql('ALTER TABLE website_posts ALTER quote TYPE VARCHAR(100)');
        $this->addSql('ALTER TABLE website_posts ALTER video TYPE VARCHAR(150)');
        $this->addSql('ALTER TABLE website_pages ALTER title TYPE VARCHAR(100)');
        $this->addSql('ALTER TABLE website_pages ALTER slug TYPE VARCHAR(100)');
        $this->addSql('ALTER TABLE website_pages ALTER description TYPE VARCHAR(150)');
        $this->addSql('ALTER TABLE website_events ADD description VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE website_events ADD quote VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE website_events ALTER title TYPE VARCHAR(100)');
        $this->addSql('ALTER TABLE website_events ALTER slug TYPE VARCHAR(100)');
    }
}
