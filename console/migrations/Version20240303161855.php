<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240303161855 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_contacts ADD profile_first_name_slug VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD profile_middle_name_slug VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD profile_last_name_slug VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD profile_company_slug VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD profile_job_title_slug VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD address_street_line1_slug VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE community_contacts ADD address_street_line2_slug VARCHAR(150) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE community_contacts DROP profile_first_name_slug');
        $this->addSql('ALTER TABLE community_contacts DROP profile_middle_name_slug');
        $this->addSql('ALTER TABLE community_contacts DROP profile_last_name_slug');
        $this->addSql('ALTER TABLE community_contacts DROP profile_company_slug');
        $this->addSql('ALTER TABLE community_contacts DROP profile_job_title_slug');
        $this->addSql('ALTER TABLE community_contacts DROP address_street_line1_slug');
        $this->addSql('ALTER TABLE community_contacts DROP address_street_line2_slug');
    }
}
