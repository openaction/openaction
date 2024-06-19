<?php

namespace App\Command\Tools;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:contacts:fix-tags',
    description: 'Fix duplicated tags.',
)]
class FixContactsTagsCommand extends Command
{
    private const CHUNK_SIZE = 5_000;

    private bool $dumpSql;
    private OutputInterface $output;
    private Connection $db;

    public function __construct(private EntityManagerInterface $em, private MessageBusInterface $bus)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('organization', InputArgument::REQUIRED, 'Organization UUID')
            ->addOption('dump-sql', null, InputOption::VALUE_NONE, 'Do not persist anything and only dump SQL to execute.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->dumpSql = $input->getOption('dump-sql');
        $this->output = $output;
        $this->db = $this->em->getConnection();

        if (!$this->dumpSql || $output->isVerbose()) {
            $output->write('Finding duplicated tags... ');
        }

        // Find duplicated tags
        $duplicatedTags = $this->db->executeQuery('
            SELECT LOWER(name) AS name, string_agg(id::text, \',\') AS ids
            FROM (SELECT * FROM community_tags ORDER BY id) as tags
            WHERE organization_id = (SELECT id FROM organizations WHERE uuid = \''.$input->getArgument('organization').'\')
            GROUP BY name
            HAVING COUNT(*) > 1
        ');

        $aliases = [];
        $names = [];
        while ($duplicatedTag = $duplicatedTags->fetchAssociative()) {
            $duplicates = explode(',', $duplicatedTag['ids']);
            $target = $duplicates[0];
            unset($duplicates[0]);

            if (count(array_values($duplicates)) > 0) {
                $names[$duplicatedTag['name']] = $duplicatedTag['name'];
                $aliases[$target] = array_values($duplicates);
            }
        }

        if (!$this->dumpSql || $output->isVerbose()) {
            $output->writeln(count($aliases).' duplicated tags found');
            $output->writeln('  => '.implode(', ', $names));
            $output->writeln('Updating tags usages ... ');
        }

        $manyToMany = [
            'community_contacts_tags' => ['tagField' => 'tag_id', 'otherField' => 'contact_id'],
            'community_emailing_campaigns_tags_filter' => ['tagField' => 'tag_id', 'otherField' => 'emailing_campaign_id'],
            'community_phoning_campaigns_tags_filter' => ['tagField' => 'tag_id', 'otherField' => 'phoning_campaign_id'],
            'community_texting_campaigns_tags_filter' => ['tagField' => 'tag_id', 'otherField' => 'texting_campaign_id'],
            'projects_tags' => ['tagField' => 'tag_id', 'otherField' => 'project_id'],
        ];

        // Migrate manyToMany relationships
        foreach ($manyToMany as $table => $fields) {
            if (!$this->dumpSql || $output->isVerbose()) {
                $output->write('  - '.$table.' ');
            }

            foreach ($aliases as $target => $duplicates) {
                // Create new relationships with the target tag
                foreach (array_chunk($duplicates, self::CHUNK_SIZE) as $chunk) {
                    $this->applySql('
                        INSERT INTO '.$table.' ('.$fields['tagField'].', '.$fields['otherField'].')
                        SELECT '.$target.', '.$fields['otherField'].' FROM '.$table.'
                        WHERE '.$fields['tagField'].' IN ('.implode(',', $chunk).')
                        ON CONFLICT DO NOTHING
                    ');

                    // Remove duplicated tags relationships
                    $this->applySql('DELETE FROM '.$table.' WHERE '.$fields['tagField'].' IN ('.implode(',', $chunk).')');
                }

                if (!$this->dumpSql || $output->isVerbose()) {
                    $output->write('.');
                }
            }

            if (!$this->dumpSql || $output->isVerbose()) {
                $output->writeln(' OK');
            }
        }

        // Migrate organizations_main_tags
        if (!$this->dumpSql || $output->isVerbose()) {
            $output->write('  - organizations_main_tags ');
        }

        foreach ($aliases as $target => $duplicates) {
            foreach (array_chunk($duplicates, self::CHUNK_SIZE) as $chunk) {
                // Create new relationships with the target tag
                $this->applySql('
                    INSERT INTO organizations_main_tags (tag_id, organization_id, weight)
                    SELECT '.$target.', organization_id, weight FROM organizations_main_tags
                    WHERE tag_id IN ('.implode(',', $chunk).')
                    ON CONFLICT DO NOTHING
                ');

                // Remove duplicated tags relationships
                $this->applySql('DELETE FROM organizations_main_tags WHERE tag_id IN ('.implode(',', $chunk).')');
            }

            if (!$this->dumpSql || $output->isVerbose()) {
                $output->write('.');
            }
        }

        if (!$this->dumpSql || $output->isVerbose()) {
            $output->writeln(' OK');
        }

        // Migrate community_email_automations
        if (!$this->dumpSql || $output->isVerbose()) {
            $output->write('  - community_email_automations ');
        }

        foreach ($aliases as $target => $duplicates) {
            foreach (array_chunk($duplicates, self::CHUNK_SIZE) as $chunk) {
                $this->applySql('
                    UPDATE community_email_automations SET tag_filter_id = '.$target.'
                    WHERE tag_filter_id IN ('.implode(',', $chunk).')
                ');
            }

            if (!$this->dumpSql || $output->isVerbose()) {
                $output->write('.');
            }
        }

        if (!$this->dumpSql || $output->isVerbose()) {
            $output->writeln(' OK');
        }

        // Delete actual tags
        if (!$this->dumpSql || $output->isVerbose()) {
            $output->write('  - community_tags ');
        }

        foreach ($aliases as $duplicates) {
            foreach (array_chunk($duplicates, self::CHUNK_SIZE) as $chunk) {
                $this->applySql('DELETE FROM community_tags WHERE id IN ('.implode(',', $chunk).')');
            }

            if (!$this->dumpSql || $output->isVerbose()) {
                $output->write('.');
            }
        }

        if (!$this->dumpSql || $output->isVerbose()) {
            $output->writeln(' OK');
        }

        return Command::SUCCESS;
    }

    private function applySql(string $sql)
    {
        if ($this->dumpSql) {
            $this->output->writeln(str_replace('    ', '', trim($sql)).';');
        } else {
            $this->db->executeStatement($sql);
        }
    }
}
