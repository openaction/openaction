<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200727194100 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE website_menu_items_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE website_menu_items (id BIGINT NOT NULL, parent_id BIGINT DEFAULT NULL, project_id BIGINT NOT NULL, position VARCHAR(20) NOT NULL, label VARCHAR(30) NOT NULL, url VARCHAR(255) NOT NULL, weight INT NOT NULL, open_new_tab INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F19F040F727ACA70 ON website_menu_items (parent_id)');
        $this->addSql('CREATE INDEX IDX_F19F040F166D1F9C ON website_menu_items (project_id)');
        $this->addSql('ALTER TABLE website_menu_items ADD CONSTRAINT FK_F19F040F727ACA70 FOREIGN KEY (parent_id) REFERENCES website_menu_items (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE website_menu_items ADD CONSTRAINT FK_F19F040F166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE projects DROP website_theme_options');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_menu_items DROP CONSTRAINT FK_F19F040F727ACA70');
        $this->addSql('DROP SEQUENCE website_menu_items_id_seq CASCADE');
        $this->addSql('DROP TABLE website_menu_items');
        $this->addSql('ALTER TABLE projects ADD website_theme_options JSON NOT NULL');
    }
}
