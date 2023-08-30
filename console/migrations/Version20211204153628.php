<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211204153628 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_phoning_campaigns ALTER end_after SET NOT NULL');
        $this->addSql('ALTER TABLE organizations ADD partner_id BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations ADD CONSTRAINT FK_427C1C7F9393F8FE FOREIGN KEY (partner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_427C1C7F9393F8FE ON organizations (partner_id)');
        $this->addSql('ALTER TABLE projects DROP website_theme');
        $this->addSql('ALTER INDEX uniq_7758e837d17f50a6 RENAME TO UNIQ_B825E171D17F50A6');
        $this->addSql('ALTER INDEX uniq_7758e83793cb796c RENAME TO UNIQ_B825E17193CB796C');
        $this->addSql('ALTER INDEX idx_7758e837166d1f9c RENAME TO IDX_B825E171166D1F9C');
        $this->addSql('ALTER TABLE users ADD is_partner BOOLEAN DEFAULT FALSE');
        $this->addSql('ALTER TABLE users ALTER is_partner DROP DEFAULT');
        $this->addSql('ALTER TABLE users ALTER is_partner SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users DROP is_partner');
        $this->addSql('ALTER TABLE projects ADD website_theme VARCHAR(25) DEFAULT NULL');
        $this->addSql('ALTER TABLE organizations DROP CONSTRAINT FK_427C1C7F9393F8FE');
        $this->addSql('DROP INDEX IDX_427C1C7F9393F8FE');
        $this->addSql('ALTER TABLE organizations DROP partner_id');
        $this->addSql('ALTER INDEX uniq_b825e17193cb796c RENAME TO uniq_7758e83793cb796c');
        $this->addSql('ALTER INDEX idx_b825e171166d1f9c RENAME TO idx_7758e837166d1f9c');
        $this->addSql('ALTER INDEX uniq_b825e171d17f50a6 RENAME TO uniq_7758e837d17f50a6');
        $this->addSql('DROP INDEX projects_tags_pkey');
        $this->addSql('ALTER TABLE projects_tags ADD PRIMARY KEY (project_id, tag_id)');
        $this->addSql('ALTER TABLE community_phoning_campaigns ALTER end_after DROP NOT NULL');
    }
}
