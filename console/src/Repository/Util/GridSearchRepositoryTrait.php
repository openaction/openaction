<?php

namespace App\Repository\Util;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Andx;
use Doctrine\ORM\Query\Expr\Orx;

trait GridSearchRepositoryTrait
{
    private int $k = 0;

    private function createAgGridFilterOperation(string $mappedField, array $filter): array
    {
        // AND/OR operator: recursive call to resolve individual filters and combine them
        if (isset($filter['operator']) && \in_array($filter['operator'], ['OR', 'AND'], true)) {
            $expr = 'OR' === $filter['operator'] ? new Orx() : new Andx();
            $params = [];

            foreach ($filter as $key => $condition) {
                if (!str_starts_with($key, 'condition')) {
                    continue;
                }

                $op = $this->createAgGridFilterOperation($mappedField, $condition);
                $params[] = $op['params'];
                $expr->add($op['expr']);
            }

            return [
                'params' => array_merge(...$params),
                'expr' => $expr,
            ];
        }

        ++$this->k;

        $exprFactory = new Expr();

        // Text filter
        if ('text' === $filter['filterType']) {
            // Contains
            if ('contains' === $filter['type']) {
                return [
                    'expr' => $exprFactory->like('LOWER('.$mappedField.')', ':k'.$this->k),
                    'params' => ['k'.$this->k => '%'.strtolower($filter['filter']).'%'],
                ];
            }

            // Not contains
            if ('notContains' === $filter['type']) {
                return [
                    'expr' => $exprFactory->notLike('LOWER('.$mappedField.')', ':k'.$this->k),
                    'params' => ['k'.$this->k => '%'.strtolower($filter['filter']).'%'],
                ];
            }

            // Equals
            if ('equals' === $filter['type']) {
                return [
                    'expr' => $exprFactory->eq('LOWER('.$mappedField.')', ':k'.$this->k),
                    'params' => ['k'.$this->k => strtolower($filter['filter'])],
                ];
            }

            // Not equals
            if ('notEqual' === $filter['type']) {
                return [
                    'expr' => $exprFactory->neq('LOWER('.$mappedField.')', ':k'.$this->k),
                    'params' => ['k'.$this->k => strtolower($filter['filter'])],
                ];
            }

            // Starts with
            if ('startsWith' === $filter['type']) {
                return [
                    'expr' => $exprFactory->like('LOWER('.$mappedField.')', ':k'.$this->k),
                    'params' => ['k'.$this->k => strtolower($filter['filter']).'%'],
                ];
            }

            // Ends with
            if ('endsWith' === $filter['type']) {
                return [
                    'expr' => $exprFactory->like('LOWER('.$mappedField.')', ':k'.$this->k),
                    'params' => ['k'.$this->k => '%'.strtolower($filter['filter'])],
                ];
            }

            // Checked
            if ('checked' === $filter['type']) {
                return [
                    'expr' => $exprFactory->eq($mappedField, ':k'.$this->k),
                    'params' => ['k'.$this->k => true],
                ];
            }

            // Unchecked
            if ('unchecked' === $filter['type']) {
                return [
                    'expr' => $exprFactory->eq($mappedField, ':k'.$this->k),
                    'params' => ['k'.$this->k => false],
                ];
            }

            // Members
            if ('member' === $filter['type']) {
                return [
                    'expr' => $exprFactory->isNotNull('c.accountPassword'),
                    'params' => [],
                ];
            }

            // Non-members
            // @phpstan-ignore-next-line
            if ('contact' === $filter['type']) {
                return [
                    'expr' => $exprFactory->isNull('c.accountPassword'),
                    'params' => [],
                ];
            }

            throw new \LogicException('Invalid type "'.$filter['type'].'" provided for text filter.');
        }

        // Date filter
        if ('date' === $filter['filterType']) {
            $dateFrom = $filter['dateFrom'] ? new \DateTimeImmutable($filter['dateFrom']) : null;
            $dateTo = $filter['dateTo'] ? new \DateTimeImmutable($filter['dateTo']) : null;

            // Equals
            if ('equals' === $filter['type']) {
                // Between date at midnight and the next day at midnight
                return [
                    'expr' => $exprFactory->andX(
                        $exprFactory->gte($mappedField, ':kf'.$this->k),
                        $exprFactory->lt($mappedField, ':kt'.$this->k)
                    ),
                    'params' => [
                        'kf'.$this->k => $dateFrom,
                        'kt'.$this->k => $dateFrom->modify('+1 day'),
                    ],
                ];
            }

            // Not equals
            if ('notEqual' === $filter['type']) {
                // Either before date at midnight or after the next day at midnight
                return [
                    'expr' => $exprFactory->orX(
                        $exprFactory->lt($mappedField, ':kf'.$this->k),
                        $exprFactory->gte($mappedField, ':kt'.$this->k)
                    ),
                    'params' => [
                        'kf'.$this->k => $dateFrom,
                        'kt'.$this->k => $dateFrom->modify('+1 day'),
                    ],
                ];
            }

            // In range
            if ('inRange' === $filter['type']) {
                // Between dateFrom at midnight and the day after dateTo at midnight
                return [
                    'expr' => $exprFactory->andX(
                        $exprFactory->gte($mappedField, ':kf'.$this->k),
                        $exprFactory->lt($mappedField, ':kt'.$this->k)
                    ),
                    'params' => [
                        'kf'.$this->k => $dateFrom,
                        'kt'.$this->k => $dateTo->modify('+1 day'),
                    ],
                ];
            }

            // Greater than
            if ('greaterThan' === $filter['type']) {
                // After next day at midnight
                return [
                    'expr' => $exprFactory->gt($mappedField, ':k'.$this->k),
                    'params' => ['k'.$this->k => $dateFrom->modify('+1 day')],
                ];
            }

            // Less than
            if ('lessThan' === $filter['type']) {
                // Before date at midnight
                return [
                    'expr' => $exprFactory->lt($mappedField, ':k'.$this->k),
                    'params' => ['k'.$this->k => $dateFrom],
                ];
            }
        }

        throw new \LogicException('Invalid filter "'.$filter['filterType'].'" provided.');
    }
}
