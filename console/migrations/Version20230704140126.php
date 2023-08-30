<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230704140126 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_email_automations ADD form_filter_id BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE community_email_automations ADD CONSTRAINT FK_E1E25137A2950D52 FOREIGN KEY (form_filter_id) REFERENCES website_forms (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_E1E25137A2950D52 ON community_email_automations (form_filter_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_email_automations DROP CONSTRAINT FK_E1E25137A2950D52');
        $this->addSql('DROP INDEX IDX_E1E25137A2950D52');
        $this->addSql('ALTER TABLE community_email_automations DROP form_filter_id');
    }
}
