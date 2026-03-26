<?php

namespace App\Command\Community;

use App\Community\MergeTag\ContactMergeTags;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:community:fix-email-merge-tags',
    description: 'Replace legacy contact merge tags in email automations and campaigns.',
)]
final class FixEmailMergeTagsCommand extends Command
{
    private const TARGETS = [
        ['table' => 'community_email_automations', 'column' => 'content', 'json' => false],
        ['table' => 'community_email_automations', 'column' => 'unlayer_design', 'json' => true],
        ['table' => 'community_emailing_campaigns', 'column' => 'content', 'json' => false],
        ['table' => 'community_emailing_campaigns', 'column' => 'unlayer_design', 'json' => true],
    ];

    public function __construct(private readonly Connection $connection)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Do not persist any replacement.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = (bool) $input->getOption('dry-run');

        $io->title($dryRun ? 'Merge tags cleanup (dry-run)' : 'Merge tags cleanup');

        $totalRows = 0;
        foreach (self::TARGETS as $target) {
            $count = $this->countRowsToUpdate($target['table'], $target['column'], $target['json']);
            $targetKey = $target['table'].'.'.$target['column'];
            $totalRows += $count;

            $io->writeln(sprintf('- %s: %d row(s)', $targetKey, $count));
        }

        if (0 === $totalRows) {
            $io->success('No legacy merge tags found.');

            return Command::SUCCESS;
        }

        if ($dryRun) {
            $io->success(sprintf('Dry-run complete: %d row(s) would be updated.', $totalRows));

            return Command::SUCCESS;
        }

        $updatedRows = 0;
        $this->connection->beginTransaction();

        try {
            foreach (self::TARGETS as $target) {
                $updated = $this->replaceRows($target['table'], $target['column'], $target['json']);
                $updatedRows += $updated;

                $io->writeln(sprintf('- updated %s.%s: %d row(s)', $target['table'], $target['column'], $updated));
            }

            $this->connection->commit();
        } catch (\Throwable $e) {
            $this->connection->rollBack();

            throw $e;
        }

        $io->success(sprintf('Cleanup complete: %d row(s) updated.', $updatedRows));

        return Command::SUCCESS;
    }

    private function countRowsToUpdate(string $table, string $column, bool $jsonColumn): int
    {
        $contentExpression = $jsonColumn ? sprintf('CAST(%s AS TEXT)', $column) : $column;
        $whereExpression = $this->buildWhereExpression($contentExpression);

        $sql = sprintf(
            'SELECT COUNT(*) FROM %s WHERE %s IS NOT NULL AND (%s)',
            $table,
            $column,
            $whereExpression,
        );

        return (int) $this->connection->fetchOne($sql);
    }

    private function replaceRows(string $table, string $column, bool $jsonColumn): int
    {
        $contentExpression = $jsonColumn ? sprintf('CAST(%s AS TEXT)', $column) : $column;
        $replacementExpression = $this->buildReplacementExpression($contentExpression);

        if ($jsonColumn) {
            $replacementExpression = sprintf('CAST(%s AS JSON)', $replacementExpression);
        }

        $whereExpression = $this->buildWhereExpression($contentExpression);
        $sql = sprintf(
            'UPDATE %s SET %s = %s WHERE %s IS NOT NULL AND (%s)',
            $table,
            $column,
            $replacementExpression,
            $column,
            $whereExpression,
        );

        return $this->connection->executeStatement($sql);
    }

    private function buildWhereExpression(string $contentExpression): string
    {
        $conditions = [];

        foreach (ContactMergeTags::LEGACY_TO_CANONICAL as $legacy => $canonical) {
            $conditions[] = sprintf(
                '%s LIKE %s',
                $contentExpression,
                $this->connection->quote('%'.$legacy.'%'),
            );
        }

        return implode(' OR ', $conditions);
    }

    private function buildReplacementExpression(string $sourceExpression): string
    {
        $expression = $sourceExpression;

        foreach (ContactMergeTags::LEGACY_TO_CANONICAL as $legacy => $canonical) {
            $expression = sprintf(
                'replace(%s, %s, %s)',
                $expression,
                $this->connection->quote($legacy),
                $this->connection->quote($canonical),
            );
        }

        return $expression;
    }
}
