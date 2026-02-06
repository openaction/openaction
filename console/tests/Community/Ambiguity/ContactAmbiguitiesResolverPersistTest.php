<?php

namespace App\Tests\Community\Ambiguity;

use App\Community\Ambiguity\ContactAmbiguitiesResolver;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;

class ContactAmbiguitiesResolverPersistTest extends TestCase
{
    public function testPersistResolvedAmbiguitiesBatchesInserts(): void
    {
        $queries = [];

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('transactional')
            ->willReturnCallback(function (callable $callback) use ($connection) {
                return $callback($connection);
            });

        $connection->method('executeStatement')
            ->willReturnCallback(function (string $sql) use (&$queries): int {
                $queries[] = $sql;

                return 1;
            });

        $resolver = new ContactAmbiguitiesResolver($connection);

        $reflection = new \ReflectionClass(ContactAmbiguitiesResolver::class);
        $constant = $reflection->getReflectionConstant('INSERT_BATCH_SIZE');
        $this->assertInstanceOf(\ReflectionClassConstant::class, $constant);
        $batchSize = $constant->getValue();

        $ambiguities = [];
        $total = $batchSize + 1;
        for ($i = 0; $i < $total; ++$i) {
            $ambiguities[] = [1, $i + 1, $i + 1001];
        }

        $resolver->persistResolvedAmbiguities($ambiguities);

        $this->assertStringContainsString('DELETE FROM community_ambiguities', $queries[0]);

        $insertQueries = array_values(array_filter($queries, static function (string $sql): bool {
            return str_contains($sql, 'INSERT INTO community_ambiguities');
        }));

        $this->assertCount(2, $insertQueries);
        $this->assertCount(3, $queries);
    }
}
