<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250909082109 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // Rename payments table and its indexes
        $this->addSql('ALTER TABLE community_contact_payments RENAME TO community_contacts_payments');
        $this->addSql('ALTER INDEX community_contact_payments_contact_idx RENAME TO community_contacts_payments_contact_idx');
        $this->addSql('ALTER INDEX community_contact_payments_type_idx RENAME TO community_contacts_payments_type_idx');

        // Rename mandates table and its index
        $this->addSql('ALTER TABLE community_contact_mandates RENAME TO community_contacts_mandates');
        $this->addSql('ALTER INDEX community_contact_mandates_contact_idx RENAME TO community_contacts_mandates_contact_idx');
    }

    // No down migration
}
