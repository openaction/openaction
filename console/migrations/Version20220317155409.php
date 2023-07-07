<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220317155409 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_manifestos_proposals DROP CONSTRAINT FK_92B853341F55203D');
        $this->addSql('ALTER TABLE website_manifestos_proposals ADD CONSTRAINT FK_92B853341F55203D FOREIGN KEY (topic_id) REFERENCES website_manifestos_topics (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_manifestos_proposals DROP CONSTRAINT fk_92b853341f55203d');
        $this->addSql('ALTER TABLE website_manifestos_proposals ADD CONSTRAINT fk_92b853341f55203d FOREIGN KEY (topic_id) REFERENCES website_manifestos_topics (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
