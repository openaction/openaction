<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240625154058 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX community_tags_name_organization_idx');
        $this->addSql('CREATE UNIQUE INDEX community_tags_name_organization_idx ON community_tags (name, organization_id)');
        $this->addSql('DROP INDEX community_contacts_email_organization_idx');
        $this->addSql('CREATE UNIQUE INDEX community_contacts_email_organization_unique_idx ON community_contacts (email, organization_id)');
    }

    public function down(Schema $schema): void
    {
    }
}
