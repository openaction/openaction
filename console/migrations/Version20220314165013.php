<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220314165013 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE analytics_website_events_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE analytics_website_events (id BIGINT NOT NULL, project_id BIGINT NOT NULL, hash UUID NOT NULL, name VARCHAR(250) NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B40013D1166D1F9C ON analytics_website_events (project_id)');
        $this->addSql('CREATE INDEX analytics_events_hash ON analytics_website_events (hash)');
        $this->addSql('COMMENT ON COLUMN analytics_website_events.hash IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE analytics_website_events ADD CONSTRAINT FK_B40013D1166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE analytics_website_page_views ADD utm_source VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE analytics_website_page_views ADD utm_medium VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE analytics_website_page_views ADD utm_campaign VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE analytics_website_page_views ADD utm_content VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE analytics_website_sessions ADD utm_source VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE analytics_website_sessions ADD utm_medium VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE analytics_website_sessions ADD utm_campaign VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE analytics_website_sessions ADD utm_content VARCHAR(100) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE analytics_website_events_id_seq CASCADE');
        $this->addSql('DROP TABLE analytics_website_events');
        $this->addSql('ALTER TABLE analytics_website_page_views DROP utm_source');
        $this->addSql('ALTER TABLE analytics_website_page_views DROP utm_medium');
        $this->addSql('ALTER TABLE analytics_website_page_views DROP utm_campaign');
        $this->addSql('ALTER TABLE analytics_website_page_views DROP utm_content');
        $this->addSql('ALTER TABLE analytics_website_sessions DROP utm_source');
        $this->addSql('ALTER TABLE analytics_website_sessions DROP utm_medium');
        $this->addSql('ALTER TABLE analytics_website_sessions DROP utm_campaign');
        $this->addSql('ALTER TABLE analytics_website_sessions DROP utm_content');
    }
}
