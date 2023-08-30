<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201015220743 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE website_forms_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE website_forms_answers_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE website_forms_blocks_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE website_forms (id BIGINT NOT NULL, project_id BIGINT NOT NULL, title VARCHAR(200) NOT NULL, slug VARCHAR(200) NOT NULL, description TEXT DEFAULT NULL, weight INT NOT NULL, propose_newsletter BOOLEAN NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, only_for_members BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AA55AB3BD17F50A6 ON website_forms (uuid)');
        $this->addSql('CREATE INDEX IDX_AA55AB3B166D1F9C ON website_forms (project_id)');
        $this->addSql('COMMENT ON COLUMN website_forms.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE website_forms_answers (id BIGINT NOT NULL, form_id BIGINT NOT NULL, contact_id BIGINT DEFAULT NULL, answers JSON NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DF47F1B7D17F50A6 ON website_forms_answers (uuid)');
        $this->addSql('CREATE INDEX IDX_DF47F1B75FF69B7D ON website_forms_answers (form_id)');
        $this->addSql('CREATE INDEX IDX_DF47F1B7E7A1254A ON website_forms_answers (contact_id)');
        $this->addSql('COMMENT ON COLUMN website_forms_answers.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE website_forms_blocks (id BIGINT NOT NULL, form_id BIGINT NOT NULL, type VARCHAR(30) NOT NULL, content TEXT NOT NULL, required BOOLEAN NOT NULL, weight INT NOT NULL, config JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3C44DBA45FF69B7D ON website_forms_blocks (form_id)');
        $this->addSql('ALTER TABLE website_forms ADD CONSTRAINT FK_AA55AB3B166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE website_forms_answers ADD CONSTRAINT FK_DF47F1B75FF69B7D FOREIGN KEY (form_id) REFERENCES website_forms (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE website_forms_answers ADD CONSTRAINT FK_DF47F1B7E7A1254A FOREIGN KEY (contact_id) REFERENCES community_contacts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE website_forms_blocks ADD CONSTRAINT FK_3C44DBA45FF69B7D FOREIGN KEY (form_id) REFERENCES website_forms (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE registrations ALTER is_admin SET NOT NULL');
        $this->addSql('ALTER TABLE registrations ALTER projects_permissions SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_forms_answers DROP CONSTRAINT FK_DF47F1B75FF69B7D');
        $this->addSql('ALTER TABLE website_forms_blocks DROP CONSTRAINT FK_3C44DBA45FF69B7D');
        $this->addSql('DROP SEQUENCE website_forms_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE website_forms_answers_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE website_forms_blocks_id_seq CASCADE');
        $this->addSql('DROP TABLE website_forms');
        $this->addSql('DROP TABLE website_forms_answers');
        $this->addSql('DROP TABLE website_forms_blocks');
        $this->addSql('ALTER TABLE registrations ALTER is_admin DROP NOT NULL');
        $this->addSql('ALTER TABLE registrations ALTER projects_permissions DROP NOT NULL');
    }
}
