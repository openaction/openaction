<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210103020545 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER SEQUENCE analytics_page_views_id_seq RENAME TO analytics_website_page_views_id_seq');
        $this->addSql('ALTER TABLE analytics_page_views RENAME TO analytics_website_page_views');
        $this->addSql('CREATE SEQUENCE analytics_website_sessions_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE analytics_website_sessions (id BIGINT NOT NULL, project_id BIGINT NOT NULL, hash UUID NOT NULL, paths_flow JSON NOT NULL, paths_count INT NOT NULL, platform VARCHAR(30) DEFAULT NULL, browser VARCHAR(30) DEFAULT NULL, country VARCHAR(2) DEFAULT NULL, original_referrer VARCHAR(100) DEFAULT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7964ACCC166D1F9C ON analytics_website_sessions (project_id)');
        $this->addSql('CREATE INDEX analytics_website_sessions_start_date ON analytics_website_sessions (start_date)');
        $this->addSql('COMMENT ON COLUMN analytics_website_sessions.hash IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE analytics_website_sessions ADD CONSTRAINT FK_7964ACCC166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER SEQUENCE analytics_website_page_views_id_seq RENAME TO analytics_page_views_id_seq');
        $this->addSql('ALTER TABLE analytics_website_page_views RENAME TO analytics_page_views');
        $this->addSql('DROP SEQUENCE analytics_website_sessions_id_seq CASCADE');
        $this->addSql('DROP TABLE analytics_website_sessions');
    }
}
