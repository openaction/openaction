<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220202151816 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations_main_tags RENAME TO organizations_main_tags_old');
        $this->addSql('DROP INDEX IDX_A74E88A632C8A3DE');
        $this->addSql('DROP INDEX IDX_A74E88A6BAD26311');

        $this->addSql('CREATE SEQUENCE organizations_main_tags_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE organizations_main_tags (id BIGINT NOT NULL, organization_id BIGINT NOT NULL, tag_id BIGINT NOT NULL, weight INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A74E88A632C8A3DE ON organizations_main_tags (organization_id)');
        $this->addSql('CREATE INDEX IDX_A74E88A6BAD26311 ON organizations_main_tags (tag_id)');
        $this->addSql('ALTER TABLE organizations_main_tags ADD CONSTRAINT FK_A74E88A632C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE organizations_main_tags ADD CONSTRAINT FK_A74E88A6BAD26311 FOREIGN KEY (tag_id) REFERENCES community_tags (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('CREATE SEQUENCE organizations_main_tags_weight_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('
            INSERT INTO organizations_main_tags (id, organization_id, tag_id, weight)
            SELECT nextval(\'organizations_main_tags_id_seq\'), ot.organization_id, ot.tag_id, nextval(\'organizations_main_tags_weight_seq\')
            FROM organizations_main_tags_old ot
            LEFT JOIN community_tags t ON ot.tag_id = t.id
            ORDER BY ot.organization_id ASC, t.name ASC
        ');

        $this->addSql('DROP TABLE organizations_main_tags_old');
        $this->addSql('DROP SEQUENCE organizations_main_tags_weight_seq CASCADE');
    }

    public function down(Schema $schema): void
    {
    }
}
