<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210126104910 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE website_manifestos_proposals_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE website_manifestos_topics_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE website_manifestos_proposals (id BIGINT NOT NULL, topic_id BIGINT NOT NULL, title VARCHAR(250) NOT NULL, content TEXT NOT NULL, status VARCHAR(25) DEFAULT NULL, status_description VARCHAR(250) DEFAULT NULL, status_cta_text VARCHAR(50) DEFAULT NULL, status_cta_url VARCHAR(250) DEFAULT NULL, weight SMALLINT NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_92B85334D17F50A6 ON website_manifestos_proposals (uuid)');
        $this->addSql('CREATE INDEX IDX_92B853341F55203D ON website_manifestos_proposals (topic_id)');
        $this->addSql('COMMENT ON COLUMN website_manifestos_proposals.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE website_manifestos_topics (id BIGINT NOT NULL, image_id BIGINT DEFAULT NULL, project_id BIGINT NOT NULL, title VARCHAR(200) NOT NULL, slug VARCHAR(200) NOT NULL, description VARCHAR(200) DEFAULT NULL, color VARCHAR(6) NOT NULL, weight SMALLINT NOT NULL, published_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BAA1CE15D17F50A6 ON website_manifestos_topics (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BAA1CE153DA5256D ON website_manifestos_topics (image_id)');
        $this->addSql('CREATE INDEX IDX_BAA1CE15166D1F9C ON website_manifestos_topics (project_id)');
        $this->addSql('COMMENT ON COLUMN website_manifestos_topics.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE website_manifestos_proposals ADD CONSTRAINT FK_92B853341F55203D FOREIGN KEY (topic_id) REFERENCES website_manifestos_topics (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE website_manifestos_topics ADD CONSTRAINT FK_BAA1CE153DA5256D FOREIGN KEY (image_id) REFERENCES uploads (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE website_manifestos_topics ADD CONSTRAINT FK_BAA1CE15166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_manifestos_proposals DROP CONSTRAINT FK_92B853341F55203D');
        $this->addSql('DROP SEQUENCE website_manifestos_proposals_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE website_manifestos_topics_id_seq CASCADE');
        $this->addSql('DROP TABLE website_manifestos_proposals');
        $this->addSql('DROP TABLE website_manifestos_topics');
    }
}
