<?php

namespace App\Bridge\Sentry;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Sentry\Breadcrumb;
use Sentry\Event;
use Sentry\EventHint;
use Sentry\Severity;
use Sentry\State\HubInterface;
use Sentry\State\Scope;

class SentryHandler extends AbstractProcessingHandler
{
    private HubInterface $hub;
    private array $breadcrumbsBuffer = [];

    /**
     * @param HubInterface $hub    The sentry hub used to send event to Sentry
     * @param int          $level  The minimum logging level at which this handler will be triggered
     * @param bool         $bubble Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct(HubInterface $hub, int $level = Logger::DEBUG, bool $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->hub = $hub;
    }

    /**
     * {@inheritdoc}
     */
    public function handleBatch(array $records): void
    {
        if (!$records) {
            return;
        }

        // Keep only records that matches the minimum level
        $records = array_filter($records, function ($record) {
            return $record['level'] >= $this->level;
        });

        // The record with the highest severity is the "main" one
        $main = array_reduce($records, static function ($highest, $record) {
            if (null === $highest || $record['level'] > $highest['level']) {
                return $record;
            }

            return $highest;
        });

        // The other ones are added as a context items
        foreach ($records as $record) {
            $record = $this->processRecord($record);
            $record['formatted'] = $this->getFormatter()->format($record);

            $this->breadcrumbsBuffer[] = $record;
        }

        $this->handle($main);
        $this->breadcrumbsBuffer = [];
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record): void
    {
        $sentryEvent = Event::createEvent();
        $sentryEvent->setLevel($sentryLevel = $this->getSeverityFromLevel($record['level']));
        $sentryEvent->setMessage((new LineFormatter('%channel%.%level_name%: %message%'))->format($record));

        $sentryHint = null;
        if (isset($record['context']['exception']) && $record['context']['exception'] instanceof \Throwable) {
            $sentryHint = new EventHint();
            $sentryHint->exception = $record['context']['exception'];
        }

        $this->hub->withScope(function (Scope $scope) use ($record, $sentryEvent, $sentryLevel, $sentryHint): void {
            $scope->setLevel($sentryLevel);
            $scope->setExtra('monolog.formatted', $record['formatted'] ?? '');

            foreach ($this->breadcrumbsBuffer as $breadcrumbRecord) {
                $scope->addBreadcrumb(new Breadcrumb(
                    $this->getBreadcrumbLevelFromLevel($breadcrumbRecord['level']),
                    $this->getBreadcrumbTypeFromLevel($breadcrumbRecord['level']),
                    $breadcrumbRecord['channel'] ?? 'N/A',
                    $breadcrumbRecord['formatted'] ?? 'N/A'
                ));
            }

            $this->hub->captureEvent($sentryEvent, $sentryHint);
        });

        $this->afterWrite();
    }

    /**
     * Extension point.
     *
     * Overridable method that for example can be used to:
     *   - disable Sentry event flush
     *   - add some custom logic after monolog write process
     *   - ...
     */
    protected function afterWrite(): void
    {
        $this->hub->getClient()->flush();
    }

    /**
     * Translates the Monolog level into the Sentry severity.
     *
     * @param int $level The Monolog log level
     */
    private function getSeverityFromLevel(int $level): Severity
    {
        switch ($level) {
            case Logger::DEBUG:
                return Severity::debug();
            case Logger::INFO:
            case Logger::NOTICE:
                return Severity::info();
            case Logger::WARNING:
                return Severity::warning();
            case Logger::ERROR:
                return Severity::error();
            default:
                return Severity::fatal();
        }
    }

    /**
     * Translates the Monolog level into the Sentry breadcrumb level.
     *
     * @param int $level The Monolog log level
     */
    private function getBreadcrumbLevelFromLevel(int $level): string
    {
        switch ($level) {
            case Logger::DEBUG:
                return Breadcrumb::LEVEL_DEBUG;
            case Logger::INFO:
            case Logger::NOTICE:
                return Breadcrumb::LEVEL_INFO;
            case Logger::WARNING:
                return Breadcrumb::LEVEL_WARNING;
            case Logger::ERROR:
                return Breadcrumb::LEVEL_ERROR;
            default:
                return Breadcrumb::LEVEL_FATAL;
        }
    }

    /**
     * Translates the Monolog level into the Sentry breadcrumb type.
     *
     * @param int $level The Monolog log level
     */
    private function getBreadcrumbTypeFromLevel(int $level): string
    {
        if ($level >= Logger::ERROR) {
            return Breadcrumb::TYPE_ERROR;
        }

        return Breadcrumb::TYPE_DEFAULT;
    }
}
