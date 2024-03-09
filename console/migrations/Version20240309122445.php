<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240309122445 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE website_events_participants (event_id BIGINT NOT NULL, trombinoscope_person_id BIGINT NOT NULL, PRIMARY KEY(event_id, trombinoscope_person_id))');
        $this->addSql('CREATE INDEX IDX_343646AA71F7E88B ON website_events_participants (event_id)');
        $this->addSql('CREATE INDEX IDX_343646AAC69EE0FA ON website_events_participants (trombinoscope_person_id)');
        $this->addSql('ALTER TABLE website_events_participants ADD CONSTRAINT FK_343646AA71F7E88B FOREIGN KEY (event_id) REFERENCES website_events (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE website_events_participants ADD CONSTRAINT FK_343646AAC69EE0FA FOREIGN KEY (trombinoscope_person_id) REFERENCES website_trombinoscope_persons (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_events_participants DROP CONSTRAINT FK_343646AA71F7E88B');
        $this->addSql('ALTER TABLE website_events_participants DROP CONSTRAINT FK_343646AAC69EE0FA');
        $this->addSql('DROP TABLE website_events_participants');
    }
}
