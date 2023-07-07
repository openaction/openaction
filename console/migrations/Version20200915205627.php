<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200915205627 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE website_candidates_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE website_candidates (id BIGINT NOT NULL, image_id BIGINT DEFAULT NULL, project_id BIGINT NOT NULL, full_name VARCHAR(100) NOT NULL, slug VARCHAR(100) NOT NULL, role VARCHAR(100) DEFAULT NULL, content TEXT NOT NULL, weight INT NOT NULL, social_email VARCHAR(150) DEFAULT NULL, social_facebook VARCHAR(150) DEFAULT NULL, social_twitter VARCHAR(150) DEFAULT NULL, social_instagram VARCHAR(150) DEFAULT NULL, social_linked_in VARCHAR(150) DEFAULT NULL, social_youtube VARCHAR(150) DEFAULT NULL, social_medium VARCHAR(150) DEFAULT NULL, social_telegram VARCHAR(32) DEFAULT NULL, published_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EB5A3629D17F50A6 ON website_candidates (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EB5A36293DA5256D ON website_candidates (image_id)');
        $this->addSql('CREATE INDEX IDX_EB5A3629166D1F9C ON website_candidates (project_id)');
        $this->addSql('COMMENT ON COLUMN website_candidates.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE website_candidates ADD CONSTRAINT FK_EB5A36293DA5256D FOREIGN KEY (image_id) REFERENCES uploads (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE website_candidates ADD CONSTRAINT FK_EB5A3629166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE website_candidates_id_seq CASCADE');
        $this->addSql('DROP TABLE website_candidates');
    }
}
