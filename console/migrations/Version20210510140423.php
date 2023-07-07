<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210510140423 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE website_trombinoscope_categories_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE website_trombinoscope_categories (id BIGINT NOT NULL, project_id BIGINT NOT NULL, name VARCHAR(40) NOT NULL, slug VARCHAR(50) NOT NULL, weight INT NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_11F3F2F2D17F50A6 ON website_trombinoscope_categories (uuid)');
        $this->addSql('CREATE INDEX IDX_11F3F2F2166D1F9C ON website_trombinoscope_categories (project_id)');
        $this->addSql('COMMENT ON COLUMN website_trombinoscope_categories.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE website_trombinoscope_persons_categories (trombinoscope_person_id BIGINT NOT NULL, trombinoscope_category_id BIGINT NOT NULL, PRIMARY KEY(trombinoscope_person_id, trombinoscope_category_id))');
        $this->addSql('CREATE INDEX IDX_F503A27EC69EE0FA ON website_trombinoscope_persons_categories (trombinoscope_person_id)');
        $this->addSql('CREATE INDEX IDX_F503A27EBF94228E ON website_trombinoscope_persons_categories (trombinoscope_category_id)');
        $this->addSql('ALTER TABLE website_trombinoscope_categories ADD CONSTRAINT FK_11F3F2F2166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE website_trombinoscope_persons_categories ADD CONSTRAINT FK_F503A27EC69EE0FA FOREIGN KEY (trombinoscope_person_id) REFERENCES website_trombinoscope_persons (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE website_trombinoscope_persons_categories ADD CONSTRAINT FK_F503A27EBF94228E FOREIGN KEY (trombinoscope_category_id) REFERENCES website_trombinoscope_categories (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_trombinoscope_persons_categories DROP CONSTRAINT FK_F503A27EBF94228E');
        $this->addSql('DROP SEQUENCE website_trombinoscope_categories_id_seq CASCADE');
        $this->addSql('DROP TABLE website_trombinoscope_categories');
        $this->addSql('DROP TABLE website_trombinoscope_persons_categories');
    }
}
