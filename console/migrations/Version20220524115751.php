<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220524115751 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_printing_campaigns_unique_scans DROP CONSTRAINT fk_6eeff4bec33f7837');
        $this->addSql('DROP SEQUENCE community_printing_campaigns_common_scans_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE community_printing_campaigns_unique_documents_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE community_printing_campaigns_unique_scans_id_seq CASCADE');
        $this->addSql('DROP TABLE community_printing_campaigns_common_scans');
        $this->addSql('DROP TABLE community_printing_campaigns_unique_documents');
        $this->addSql('DROP TABLE community_printing_campaigns_unique_scans');
        $this->addSql('ALTER TABLE community_printing_campaigns DROP qr_code');
        $this->addSql('ALTER TABLE projects DROP website_custom_frontend');
    }

    public function down(Schema $schema): void
    {
    }
}
