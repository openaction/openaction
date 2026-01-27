<?php

namespace App\Tests\PHPUnit;

use DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver;
use DAMA\DoctrineTestBundle\PHPUnit\SkipDatabaseRollback;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\AfterLastTestHook;
use PHPUnit\Runner\BeforeFirstTestHook;
use PHPUnit\Runner\BeforeTestHook;

final class DoctrineTestBundleExtension implements BeforeFirstTestHook, BeforeTestHook, AfterLastTestHook
{
    private static bool $transactionStarted = false;
    private static bool $transactionSkipped = false;

    public function executeBeforeFirstTest(): void
    {
        StaticDriver::setKeepStaticConnections(true);
    }

    public function executeBeforeTest(string $test): void
    {
        self::rollBack();

        if ($this->hasSkipAttribute($test)) {
            self::skipTransaction();

            return;
        }

        self::unskipTransaction();
        self::beginTransaction();
    }

    public function executeAfterLastTest(): void
    {
        self::rollBack();
        StaticDriver::setKeepStaticConnections(false);
    }

    private static function beginTransaction(): void
    {
        if (self::$transactionStarted) {
            return;
        }

        StaticDriver::beginTransaction();
        self::$transactionStarted = true;
    }

    private static function rollBack(): void
    {
        if (!self::$transactionStarted) {
            return;
        }

        StaticDriver::rollBack();
        self::$transactionStarted = false;
    }

    private static function skipTransaction(): void
    {
        self::$transactionSkipped = true;
        StaticDriver::setKeepStaticConnections(false);
    }

    private static function unskipTransaction(): void
    {
        if (!self::$transactionSkipped) {
            return;
        }

        self::$transactionSkipped = false;
        StaticDriver::setKeepStaticConnections(true);
    }

    private function hasSkipAttribute(string $test): bool
    {
        $parsed = $this->parseTestReference($test);
        if (null === $parsed) {
            return false;
        }

        [$className, $methodName] = $parsed;

        if (!class_exists($className)) {
            return false;
        }

        $reflectionClass = new \ReflectionClass($className);
        if ($reflectionClass->getAttributes(SkipDatabaseRollback::class)) {
            return true;
        }

        if ($reflectionClass->hasMethod($methodName)
            && $reflectionClass->getMethod($methodName)->getAttributes(SkipDatabaseRollback::class)
        ) {
            return true;
        }

        while (($reflectionClass = $reflectionClass->getParentClass())
            && TestCase::class !== $reflectionClass->name
        ) {
            if ($reflectionClass->getAttributes(SkipDatabaseRollback::class)) {
                return true;
            }
        }

        return false;
    }

    private function parseTestReference(string $test): ?array
    {
        $normalized = preg_split('/\s+with data set.*/', $test, 2)[0] ?? '';

        if (!str_contains($normalized, '::')) {
            return null;
        }

        $parts = explode('::', $normalized, 2);
        if (2 !== count($parts)) {
            return null;
        }

        return [$parts[0], $parts[1]];
    }
}
