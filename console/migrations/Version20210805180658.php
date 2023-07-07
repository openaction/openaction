<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210805180658 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations ADD texts_credits_balance BIGINT DEFAULT 0');
        $this->addSql('ALTER TABLE organizations ALTER texts_credits_balance DROP DEFAULT');
        $this->addSql('ALTER TABLE organizations ALTER texts_credits_balance SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations DROP texts_credits_balance');
    }
}
