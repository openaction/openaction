<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210205182842 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects ADD appearance_terminology JSON DEFAULT \'{"posts":"Actualit\u00e9s","events":"\u00c9v\u00e9nements","trombinoscope":"Notre \u00e9quipe","manifesto":"Nos propositions","newsletter":"Recevoir la newsletter","socialNetworks":"R\u00e9seaux sociaux"}\'');
        $this->addSql('ALTER TABLE projects ALTER appearance_terminology DROP DEFAULT');
        $this->addSql('ALTER TABLE projects ALTER appearance_terminology SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects DROP appearance_terminology');
    }
}
