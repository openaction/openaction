<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220512090531 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects ADD emailing_domain_id BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A48E9FE9ED FOREIGN KEY (emailing_domain_id) REFERENCES domains (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5C93B3A48E9FE9ED ON projects (emailing_domain_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projects DROP CONSTRAINT FK_5C93B3A48E9FE9ED');
        $this->addSql('DROP INDEX IDX_5C93B3A48E9FE9ED');
        $this->addSql('ALTER TABLE projects DROP emailing_domain_id');
    }
}
