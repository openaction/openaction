<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210308215650 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_events ADD page_views BIGINT DEFAULT 0');
        $this->addSql('ALTER TABLE website_events ALTER page_views DROP DEFAULT');
        $this->addSql('ALTER TABLE website_events ALTER page_views SET NOT NULL');

        $this->addSql('ALTER TABLE website_forms ADD page_views BIGINT DEFAULT 0');
        $this->addSql('ALTER TABLE website_forms ALTER page_views DROP DEFAULT');
        $this->addSql('ALTER TABLE website_forms ALTER page_views SET NOT NULL');

        $this->addSql('ALTER TABLE website_pages ADD page_views BIGINT DEFAULT 0');
        $this->addSql('ALTER TABLE website_pages ALTER page_views DROP DEFAULT');
        $this->addSql('ALTER TABLE website_pages ALTER page_views SET NOT NULL');

        $this->addSql('ALTER TABLE website_posts ADD page_views BIGINT DEFAULT 0');
        $this->addSql('ALTER TABLE website_posts ALTER page_views DROP DEFAULT');
        $this->addSql('ALTER TABLE website_posts ALTER page_views SET NOT NULL');

        $this->addSql('ALTER TABLE website_manifestos_topics ADD page_views BIGINT DEFAULT 0');
        $this->addSql('ALTER TABLE website_manifestos_topics ALTER page_views DROP DEFAULT');
        $this->addSql('ALTER TABLE website_manifestos_topics ALTER page_views SET NOT NULL');

        $this->addSql('ALTER TABLE website_trombinoscope_persons ADD page_views BIGINT DEFAULT 0');
        $this->addSql('ALTER TABLE website_trombinoscope_persons ALTER page_views DROP DEFAULT');
        $this->addSql('ALTER TABLE website_trombinoscope_persons ALTER page_views SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_pages DROP page_views');
        $this->addSql('ALTER TABLE website_posts DROP page_views');
        $this->addSql('ALTER TABLE website_events DROP page_views');
        $this->addSql('ALTER TABLE website_forms DROP page_views');
        $this->addSql('ALTER TABLE website_manifestos_topics DROP page_views');
    }
}
