<?php

namespace App\Command\Community;

use App\Util\Address;
use App\Util\PhoneNumber;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[AsCommand(
    name: 'app:community:clean',
    description: 'Clean contacts data by normalizing city names, parsing phone numbers, computing slugs, ...',
)]
class CleanContactsCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $db = $this->em->getConnection();

        $io->text('Computing missing slugs');
        $this->computeMissingSlugs($io, $db);
        $io->success('Missing slugs computed');

        $io->text('Normalizing cities');
        $this->normalizeCities($io, $db);
        $io->success('Cities normalized');

        $io->text('Normalizing zip codes');
        $this->normalizeZipCodes($db);
        $io->success('Zip codes normalized');

        $io->text('Parsing phone numbers');
        $this->parsePhoneNumbers($io, $db);
        $io->success('Phone numbers parsed');

        return Command::SUCCESS;
    }

    private function computeMissingSlugs(SymfonyStyle $io, Connection $db): void
    {
        $slugger = new AsciiSlugger();

        $sqlWhere = '
            (profile_first_name IS NOT NULL AND profile_first_name != \'\' AND profile_first_name_slug IS NULL) 
            OR (profile_middle_name IS NOT NULL AND profile_middle_name != \'\' AND profile_middle_name_slug IS NULL) 
            OR (profile_last_name IS NOT NULL AND profile_last_name != \'\' AND profile_last_name_slug IS NULL) 
            OR (profile_company IS NOT NULL AND profile_company != \'\' AND profile_company_slug IS NULL) 
            OR (profile_job_title IS NOT NULL AND profile_job_title != \'\' AND profile_job_title_slug IS NULL) 
            OR (address_street_line1 IS NOT NULL AND address_street_line1 != \'\' AND address_street_line1_slug IS NULL) 
            OR (address_street_line2 IS NOT NULL AND address_street_line2 != \'\' AND address_street_line2_slug IS NULL)
        ';

        $totalCount = $db->executeQuery('SELECT COUNT(*) AS count FROM community_contacts WHERE '.$sqlWhere)->fetchAssociative()['count'];

        $rows = $db->executeQuery('
            SELECT id, profile_first_name, profile_middle_name, profile_last_name, profile_company, 
                   profile_job_title, address_street_line1, address_street_line2
            FROM community_contacts
            WHERE '.$sqlWhere
        );

        $progress = new ProgressBar($io, $totalCount);

        while ($row = $rows->fetchAssociative()) {
            $db->executeStatement(
                sql: '
                    UPDATE community_contacts 
                    SET profile_first_name_slug = ?, profile_middle_name_slug = ?, profile_last_name_slug = ?,
                        profile_company_slug = ?, profile_job_title_slug = ?, address_street_line1_slug = ?,
                        address_street_line2_slug = ?
                    WHERE id = ?
                ',
                params: [
                    $row['profile_first_name'] ? $slugger->slug($row['profile_first_name'], '-')->lower()->toString() : null,
                    $row['profile_middle_name'] ? $slugger->slug($row['profile_middle_name'], '-')->lower()->toString() : null,
                    $row['profile_last_name'] ? $slugger->slug($row['profile_last_name'], '-')->lower()->toString() : null,
                    $row['profile_company'] ? $slugger->slug($row['profile_company'], '-')->lower()->toString() : null,
                    $row['profile_job_title'] ? $slugger->slug($row['profile_job_title'], '-')->lower()->toString() : null,
                    $row['address_street_line1'] ? $slugger->slug($row['address_street_line1'], '-')->lower()->toString() : null,
                    $row['address_street_line2'] ? $slugger->slug($row['address_street_line2'], '-')->lower()->toString() : null,
                    $row['id'],
                ],
            );

            $progress->advance();
        }

        $progress->finish();
    }

    private function normalizeCities(SymfonyStyle $io, Connection $db): void
    {
        $totalCount = $db
            ->executeQuery('SELECT COUNT(*) AS count FROM community_contacts WHERE address_city IS NOT NULL AND address_city != \'\'')
            ->fetchAssociative()['count'];

        $rows = $db->executeQuery('SELECT id, address_city FROM community_contacts WHERE address_city IS NOT NULL AND address_city != \'\'');

        $progress = new ProgressBar($io, $totalCount);

        while ($row = $rows->fetchAssociative()) {
            $db->executeStatement(
                sql: 'UPDATE community_contacts SET address_city = ? WHERE id = ?',
                params: [Address::formatCityName($row['address_city']), $row['id']],
            );

            $progress->advance();
        }

        $progress->finish();
    }

    private function normalizeZipCodes(Connection $db): void
    {
        $db->executeStatement('
            UPDATE community_contacts 
            SET address_zip_code = replace(address_zip_code, \' \', \'\') 
            WHERE address_zip_code IS NOT NULL AND address_zip_code != \'\'
        ');
    }

    private function parsePhoneNumbers(SymfonyStyle $io, Connection $db): void
    {
        $sqlWhere = '
            (contact_phone IS NOT NULL AND contact_phone != \'\' AND parsed_contact_phone IS NULL) 
            OR (contact_work_phone IS NOT NULL AND contact_work_phone != \'\' AND parsed_contact_work_phone IS NULL)
        ';

        $totalCount = $db->executeQuery('SELECT COUNT(*) AS count FROM community_contacts WHERE '.$sqlWhere)->fetchAssociative()['count'];

        $rows = $db->executeQuery('
            SELECT c.id, c.contact_phone, c.contact_work_phone, LOWER(ac.code) AS address_country_code 
            FROM community_contacts c
            LEFT JOIN areas ac ON address_country_id = ac.id
            WHERE '.$sqlWhere
        );

        $progress = new ProgressBar($io, $totalCount);

        while ($row = $rows->fetchAssociative()) {
            $db->executeStatement(
                sql: 'UPDATE community_contacts SET parsed_contact_phone = ?, parsed_contact_work_phone = ? WHERE id = ?',
                params: [
                    $row['contact_phone'] ? PhoneNumber::formatDatabase(PhoneNumber::parse($row['contact_phone'], $row['address_country_code'] ?: 'fr')) : null,
                    $row['contact_work_phone'] ? PhoneNumber::formatDatabase(PhoneNumber::parse($row['contact_work_phone'], $row['address_country_code'] ?: 'fr')) : null,
                    $row['id'],
                ],
            );

            $progress->advance();
        }

        $progress->finish();
    }
}
