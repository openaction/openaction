<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260129120000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE website_themes_templates (id BIGSERIAL NOT NULL, theme_id BIGINT NOT NULL, name VARCHAR(100) NOT NULL, content TEXT NOT NULL, path VARCHAR(255) DEFAULT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4E43B8D9D17F50A6 ON website_themes_templates (uuid)');
        $this->addSql('CREATE INDEX IDX_4E43B8D959027487 ON website_themes_templates (theme_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4E43B8D95E237E06 ON website_themes_templates (theme_id, name)');
        $this->addSql('COMMENT ON COLUMN website_themes_templates.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE website_themes_templates ADD CONSTRAINT FK_4E43B8D959027487 FOREIGN KEY (theme_id) REFERENCES website_themes (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('INSERT INTO website_themes_templates (theme_id, name, content, path, uuid, created_at, updated_at) SELECT id, key, value, NULL, gen_random_uuid(), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM website_themes, json_each_text(CASE WHEN json_typeof(templates) = \'object\' THEN templates ELSE \'{}\'::json END)');
        $this->addSql('ALTER TABLE website_themes DROP templates');
    }

    public function down(Schema $schema): void
    {
    }
}
