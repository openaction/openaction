<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220412125238 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_printing_campaigns ADD source_id BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE community_printing_campaigns ADD preview_id BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE community_printing_campaigns ALTER product TYPE VARCHAR(40)');
        $this->addSql('ALTER TABLE community_printing_campaigns ALTER product DROP DEFAULT');
        $this->addSql('ALTER TABLE community_printing_campaigns ADD CONSTRAINT FK_99020814953C1C61 FOREIGN KEY (source_id) REFERENCES uploads (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE community_printing_campaigns ADD CONSTRAINT FK_99020814CDE46FDB FOREIGN KEY (preview_id) REFERENCES uploads (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_99020814953C1C61 ON community_printing_campaigns (source_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_99020814CDE46FDB ON community_printing_campaigns (preview_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_printing_campaigns DROP CONSTRAINT FK_99020814953C1C61');
        $this->addSql('ALTER TABLE community_printing_campaigns DROP CONSTRAINT FK_99020814CDE46FDB');
        $this->addSql('DROP INDEX UNIQ_99020814953C1C61');
        $this->addSql('DROP INDEX UNIQ_99020814CDE46FDB');
        $this->addSql('ALTER TABLE community_printing_campaigns DROP source_id');
        $this->addSql('ALTER TABLE community_printing_campaigns DROP preview_id');
        $this->addSql('ALTER TABLE community_printing_campaigns ALTER product TYPE JSON');
        $this->addSql('ALTER TABLE community_printing_campaigns ALTER product DROP DEFAULT');
        $this->addSql('ALTER TABLE community_printing_campaigns ALTER product TYPE JSON');
    }
}
