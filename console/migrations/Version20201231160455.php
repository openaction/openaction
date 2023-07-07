<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201231160455 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE organizations ADD subscription_plan VARCHAR(30) DEFAULT \'premium\'');
        $this->addSql('ALTER TABLE organizations ALTER subscription_plan DROP DEFAULT');
        $this->addSql('ALTER TABLE organizations ALTER subscription_plan SET NOT NULL');
        $this->addSql('ALTER INDEX uniq_eb5a3629d17f50a6 RENAME TO UNIQ_C16F361ED17F50A6');
        $this->addSql('ALTER INDEX uniq_eb5a36293da5256d RENAME TO UNIQ_C16F361E3DA5256D');
        $this->addSql('ALTER INDEX idx_eb5a3629166d1f9c RENAME TO IDX_C16F361E166D1F9C');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER INDEX uniq_c16f361e3da5256d RENAME TO uniq_eb5a36293da5256d');
        $this->addSql('ALTER INDEX uniq_c16f361ed17f50a6 RENAME TO uniq_eb5a3629d17f50a6');
        $this->addSql('ALTER INDEX idx_c16f361e166d1f9c RENAME TO idx_eb5a3629166d1f9c');
        $this->addSql('ALTER TABLE organizations DROP subscription_plan');
    }
}
