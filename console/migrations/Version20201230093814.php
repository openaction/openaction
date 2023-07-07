<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201230093814 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER SEQUENCE website_candidates_id_seq RENAME TO website_trombinoscope_persons_id_seq');
        $this->addSql('ALTER TABLE website_candidates RENAME TO website_trombinoscope_persons');

        // Remove program module for now, replace members area module name and replace officials by trombinoscope
        $this->addSql('
            UPDATE projects
            SET tools = REPLACE(
                    REPLACE(
                        REPLACE(tools, \',politics_program\', \'\'),
                        \'website_members_area\',
                        \'members_area_account\'
                    ),
                    \'politics_officials\',
                    \'website_trombinoscope\'
                ),
                modules = REPLACE(modules, \',politics\', \'\')
            ;
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER SEQUENCE website_trombinoscope_persons_id_seq RENAME TO website_candidates_id_seq');
        $this->addSql('ALTER TABLE website_trombinoscope_persons RENAME TO website_candidates');
    }
}
