<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201104200227 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE analytics_page_views_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE analytics_page_views (id BIGINT NOT NULL, project_id BIGINT NOT NULL, hash UUID NOT NULL, path VARCHAR(250) NOT NULL, platform VARCHAR(30) DEFAULT NULL, browser VARCHAR(30) DEFAULT NULL, country VARCHAR(2) DEFAULT NULL, referrer VARCHAR(100) DEFAULT NULL, referrer_path VARCHAR(250) DEFAULT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_67C528A2166D1F9C ON analytics_page_views (project_id)');
        $this->addSql('CREATE INDEX analytics_page_views_hash ON analytics_page_views (hash)');
        $this->addSql('COMMENT ON COLUMN analytics_page_views.hash IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE analytics_page_views ADD CONSTRAINT FK_67C528A2166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE analytics_page_views_id_seq CASCADE');
        $this->addSql('DROP TABLE analytics_page_views');
    }
}
