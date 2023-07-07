<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200716214220 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE website_events_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE website_events_categories_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE website_events (id BIGINT NOT NULL, image_id BIGINT DEFAULT NULL, project_id BIGINT NOT NULL, title VARCHAR(100) NOT NULL, slug VARCHAR(100) NOT NULL, description VARCHAR(150) DEFAULT NULL, quote VARCHAR(100) DEFAULT NULL, content TEXT NOT NULL, published_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, begin_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, url VARCHAR(250) DEFAULT NULL, button_text VARCHAR(35) DEFAULT NULL, latitude NUMERIC(10, 7) DEFAULT NULL, longitude NUMERIC(10, 7) DEFAULT NULL, address VARCHAR(250) DEFAULT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, only_for_members BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C102B361D17F50A6 ON website_events (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C102B3613DA5256D ON website_events (image_id)');
        $this->addSql('CREATE INDEX IDX_C102B361166D1F9C ON website_events (project_id)');
        $this->addSql('COMMENT ON COLUMN website_events.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE website_events_events_categories (event_id BIGINT NOT NULL, event_category_id BIGINT NOT NULL, PRIMARY KEY(event_id, event_category_id))');
        $this->addSql('CREATE INDEX IDX_41F5751E71F7E88B ON website_events_events_categories (event_id)');
        $this->addSql('CREATE INDEX IDX_41F5751EB9CF4E62 ON website_events_events_categories (event_category_id)');
        $this->addSql('CREATE TABLE website_events_categories (id BIGINT NOT NULL, project_id BIGINT NOT NULL, name VARCHAR(40) NOT NULL, slug VARCHAR(50) NOT NULL, weight INT NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_35D9A0FDD17F50A6 ON website_events_categories (uuid)');
        $this->addSql('CREATE INDEX IDX_35D9A0FD166D1F9C ON website_events_categories (project_id)');
        $this->addSql('COMMENT ON COLUMN website_events_categories.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE website_events ADD CONSTRAINT FK_C102B3613DA5256D FOREIGN KEY (image_id) REFERENCES uploads (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE website_events ADD CONSTRAINT FK_C102B361166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE website_events_events_categories ADD CONSTRAINT FK_41F5751E71F7E88B FOREIGN KEY (event_id) REFERENCES website_events (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE website_events_events_categories ADD CONSTRAINT FK_41F5751EB9CF4E62 FOREIGN KEY (event_category_id) REFERENCES website_events_categories (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE website_events_categories ADD CONSTRAINT FK_35D9A0FD166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE website_posts ADD only_for_members BOOLEAN DEFAULT FALSE');
        $this->addSql('ALTER TABLE website_pages ADD only_for_members BOOLEAN DEFAULT FALSE');
        $this->addSql('ALTER TABLE website_documents ADD only_for_members BOOLEAN DEFAULT FALSE');
        $this->addSql('ALTER TABLE website_posts ALTER only_for_members DROP DEFAULT');
        $this->addSql('ALTER TABLE website_posts ALTER only_for_members SET NOT NULL');
        $this->addSql('ALTER TABLE website_pages ALTER only_for_members DROP DEFAULT');
        $this->addSql('ALTER TABLE website_pages ALTER only_for_members SET NOT NULL');
        $this->addSql('ALTER TABLE website_documents ALTER only_for_members DROP DEFAULT');
        $this->addSql('ALTER TABLE website_documents ALTER only_for_members SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_events_events_categories DROP CONSTRAINT FK_41F5751E71F7E88B');
        $this->addSql('ALTER TABLE website_events_events_categories DROP CONSTRAINT FK_41F5751EB9CF4E62');
        $this->addSql('DROP SEQUENCE website_events_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE website_events_categories_id_seq CASCADE');
        $this->addSql('DROP TABLE website_events');
        $this->addSql('DROP TABLE website_events_events_categories');
        $this->addSql('DROP TABLE website_events_categories');
        $this->addSql('ALTER TABLE website_posts DROP only_for_members');
        $this->addSql('ALTER TABLE website_pages DROP only_for_members');
        $this->addSql('ALTER TABLE website_documents DROP only_for_members');
    }
}
