<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201122143215 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_emailing_campaigns ADD only_for_members BOOLEAN DEFAULT FALSE');
        $this->addSql('ALTER TABLE community_emailing_campaigns ALTER only_for_members DROP DEFAULT');
        $this->addSql('ALTER TABLE community_emailing_campaigns ALTER only_for_members SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_emailing_campaigns DROP only_for_members');
    }
}
