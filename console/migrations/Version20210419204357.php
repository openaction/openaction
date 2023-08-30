<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210419204357 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE organizations_main_tags (organization_id BIGINT NOT NULL, tag_id BIGINT NOT NULL, PRIMARY KEY(organization_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_A74E88A632C8A3DE ON organizations_main_tags (organization_id)');
        $this->addSql('CREATE INDEX IDX_A74E88A6BAD26311 ON organizations_main_tags (tag_id)');
        $this->addSql('ALTER TABLE organizations_main_tags ADD CONSTRAINT FK_A74E88A632C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE organizations_main_tags ADD CONSTRAINT FK_A74E88A6BAD26311 FOREIGN KEY (tag_id) REFERENCES community_tags (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE organizations RENAME COLUMN contacts_flags_is_progress TO main_tags_is_progress');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE organizations_main_tags');
        $this->addSql('ALTER TABLE organizations RENAME COLUMN main_tags_is_progress TO contacts_flags_is_progress');
    }
}
