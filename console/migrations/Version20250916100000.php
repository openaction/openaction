<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250916100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Website petitions base schema';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE website_petitions (id BIGSERIAL NOT NULL, project_id BIGINT NOT NULL, slug VARCHAR(200) NOT NULL, published_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, start_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, end_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, signatures_goal INT DEFAULT NULL, signatures_count INT DEFAULT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, only_for_members BOOLEAN NOT NULL, page_views BIGINT NOT NULL, external_url VARCHAR(250) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_11202FEED17F50A6 ON website_petitions (uuid)');
        $this->addSql('CREATE INDEX IDX_11202FEE166D1F9C ON website_petitions (project_id)');
        $this->addSql("COMMENT ON COLUMN website_petitions.uuid IS '(DC2Type:uuid)'");
        $this->addSql('CREATE TABLE website_petitions_authors (petition_id BIGINT NOT NULL, trombinoscope_person_id BIGINT NOT NULL, PRIMARY KEY(petition_id, trombinoscope_person_id))');
        $this->addSql('CREATE INDEX IDX_FC70E0EAEC7D346 ON website_petitions_authors (petition_id)');
        $this->addSql('CREATE INDEX IDX_FC70E0EC69EE0FA ON website_petitions_authors (trombinoscope_person_id)');
        $this->addSql('CREATE TABLE website_petitions_localized (id BIGSERIAL NOT NULL, petition_id BIGINT NOT NULL, form_id BIGINT DEFAULT NULL, image_id BIGINT DEFAULT NULL, locale VARCHAR(10) NOT NULL, title VARCHAR(200) NOT NULL, description VARCHAR(200) DEFAULT NULL, content TEXT DEFAULT NULL, submit_button_label VARCHAR(30) DEFAULT NULL, optin_label VARCHAR(30) DEFAULT NULL, legalities TEXT DEFAULT NULL, addressed_to VARCHAR(200) DEFAULT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql("COMMENT ON COLUMN website_petitions_localized.uuid IS '(DC2Type:uuid)'");
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3C9C8FEFD17F50A6 ON website_petitions_localized (uuid)');
        $this->addSql('CREATE INDEX IDX_3C9C8FEFAEC7D346 ON website_petitions_localized (petition_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3C9C8FEF5FF69B7D ON website_petitions_localized (form_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3C9C8FEFE4873418 ON website_petitions_localized (image_id)');
        $this->addSql('CREATE TABLE website_petitions_localized_petitions_localized_categories (petition_localized_id BIGINT NOT NULL, petition_category_id BIGINT NOT NULL, PRIMARY KEY(petition_localized_id, petition_category_id))');
        $this->addSql('CREATE INDEX IDX_73F58100BF5C489E ON website_petitions_localized_petitions_localized_categories (petition_localized_id)');
        $this->addSql('CREATE INDEX IDX_73F5810063EBEEFE ON website_petitions_localized_petitions_localized_categories (petition_category_id)');
        $this->addSql('CREATE TABLE website_petitions_localized_categories (id BIGSERIAL NOT NULL, project_id BIGINT NOT NULL, name VARCHAR(40) NOT NULL, slug VARCHAR(50) NOT NULL, weight INT NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4B844E5D17F50A6 ON website_petitions_localized_categories (uuid)');
        $this->addSql('CREATE INDEX IDX_4B844E5166D1F9C ON website_petitions_localized_categories (project_id)');
        $this->addSql("COMMENT ON COLUMN website_petitions_localized_categories.uuid IS '(DC2Type:uuid)'");
        $this->addSql('ALTER TABLE website_petitions ADD CONSTRAINT FK_11202FEE166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE website_petitions_authors ADD CONSTRAINT FK_FC70E0EAEC7D346 FOREIGN KEY (petition_id) REFERENCES website_petitions (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE website_petitions_authors ADD CONSTRAINT FK_FC70E0EC69EE0FA FOREIGN KEY (trombinoscope_person_id) REFERENCES website_trombinoscope_persons (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE website_petitions_localized ADD CONSTRAINT FK_3C9C8FEFAEC7D346 FOREIGN KEY (petition_id) REFERENCES website_petitions (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE website_petitions_localized ADD CONSTRAINT FK_3C9C8FEF5FF69B7D FOREIGN KEY (form_id) REFERENCES website_forms (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE website_petitions_localized ADD CONSTRAINT FK_3C9C8FEFE4873418 FOREIGN KEY (image_id) REFERENCES uploads (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE website_petitions_localized_petitions_localized_categories ADD CONSTRAINT FK_73F58100BF5C489E FOREIGN KEY (petition_localized_id) REFERENCES website_petitions_localized (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE website_petitions_localized_petitions_localized_categories ADD CONSTRAINT FK_73F5810063EBEEFE FOREIGN KEY (petition_category_id) REFERENCES website_petitions_localized_categories (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE website_petitions_localized_categories ADD CONSTRAINT FK_4B844E5166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_petitions_localized_petitions_localized_categories DROP CONSTRAINT FK_73F58100BF5C489E');
        $this->addSql('ALTER TABLE website_petitions_localized_petitions_localized_categories DROP CONSTRAINT FK_73F5810063EBEEFE');
        $this->addSql('ALTER TABLE website_petitions_localized DROP CONSTRAINT FK_3C9C8FEFAEC7D346');
        $this->addSql('ALTER TABLE website_petitions_localized DROP CONSTRAINT FK_3C9C8FEF5FF69B7D');
        $this->addSql('ALTER TABLE website_petitions_localized DROP CONSTRAINT FK_3C9C8FEFE4873418');
        $this->addSql('ALTER TABLE website_petitions_localized_categories DROP CONSTRAINT FK_4B844E5166D1F9C');
        $this->addSql('ALTER TABLE website_petitions_authors DROP CONSTRAINT FK_FC70E0EAEC7D346');
        $this->addSql('ALTER TABLE website_petitions_authors DROP CONSTRAINT FK_FC70E0EC69EE0FA');
        $this->addSql('ALTER TABLE website_petitions DROP CONSTRAINT FK_11202FEE166D1F9C');
        $this->addSql('DROP TABLE website_petitions_localized_petitions_localized_categories');
        $this->addSql('DROP TABLE website_petitions_localized');
        $this->addSql('DROP TABLE website_petitions_localized_categories');
        $this->addSql('DROP TABLE website_petitions_authors');
        $this->addSql('DROP TABLE website_petitions');
    }
}
