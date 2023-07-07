<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211129153714 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE website_themes_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE website_themes_assets_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE website_themes (id BIGINT NOT NULL, author_id BIGINT DEFAULT NULL, thumbnail_id BIGINT DEFAULT NULL, installation_id VARCHAR(20) DEFAULT NULL, repository_node_id VARCHAR(50) DEFAULT NULL, repository_full_name VARCHAR(200) DEFAULT NULL, is_updating BOOLEAN NOT NULL, update_error VARCHAR(200) DEFAULT NULL, name JSON NOT NULL, description JSON NOT NULL, templates JSON NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_87C7D6F5D17F50A6 ON website_themes (uuid)');
        $this->addSql('CREATE INDEX IDX_87C7D6F5F675F31B ON website_themes (author_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_87C7D6F5FDFF2E92 ON website_themes (thumbnail_id)');
        $this->addSql('CREATE INDEX website_themes_installation_id ON website_themes (installation_id)');
        $this->addSql('CREATE INDEX website_themes_repository_node_id ON website_themes (repository_node_id)');
        $this->addSql('COMMENT ON COLUMN website_themes.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE organizations_website_themes (website_theme_id BIGINT NOT NULL, organization_id BIGINT NOT NULL, PRIMARY KEY(website_theme_id, organization_id))');
        $this->addSql('CREATE INDEX IDX_B93BD31A88153D0A ON organizations_website_themes (website_theme_id)');
        $this->addSql('CREATE INDEX IDX_B93BD31A32C8A3DE ON organizations_website_themes (organization_id)');
        $this->addSql('CREATE TABLE website_themes_assets (id BIGINT NOT NULL, theme_id BIGINT NOT NULL, file_id BIGINT DEFAULT NULL, pathname VARCHAR(250) NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5978CA08D17F50A6 ON website_themes_assets (uuid)');
        $this->addSql('CREATE INDEX IDX_5978CA0859027487 ON website_themes_assets (theme_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5978CA0893CB796C ON website_themes_assets (file_id)');
        $this->addSql('COMMENT ON COLUMN website_themes_assets.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE website_themes ADD CONSTRAINT FK_87C7D6F5F675F31B FOREIGN KEY (author_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE website_themes ADD CONSTRAINT FK_87C7D6F5FDFF2E92 FOREIGN KEY (thumbnail_id) REFERENCES uploads (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE organizations_website_themes ADD CONSTRAINT FK_B93BD31A88153D0A FOREIGN KEY (website_theme_id) REFERENCES website_themes (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE organizations_website_themes ADD CONSTRAINT FK_B93BD31A32C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE website_themes_assets ADD CONSTRAINT FK_5978CA0859027487 FOREIGN KEY (theme_id) REFERENCES website_themes (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE website_themes_assets ADD CONSTRAINT FK_5978CA0893CB796C FOREIGN KEY (file_id) REFERENCES uploads (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations_website_themes DROP CONSTRAINT FK_B93BD31A88153D0A');
        $this->addSql('ALTER TABLE website_themes_assets DROP CONSTRAINT FK_5978CA0859027487');
        $this->addSql('DROP SEQUENCE website_themes_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE website_themes_assets_id_seq CASCADE');
        $this->addSql('DROP TABLE website_themes');
        $this->addSql('DROP TABLE organizations_website_themes');
        $this->addSql('DROP TABLE website_themes_assets');
    }
}
