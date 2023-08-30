<?php

namespace App\Bridge\Sentry;

use App\Messenger\Exception\RestartWorkerException;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Sentry\Breadcrumb;
use Sentry\Event;
use Sentry\EventHint;
use Sentry\Severity;
use Sentry\State\HubInterface;
use Sentry\State\Scope;
use Symfony\Component\Messenger\Exception\RejectRedeliveredMessageException;

class SentryHandler extends AbstractProcessingHandler
{
    private HubInterface $hub;
    private array $breadcrumbsBuffer = [];

    private const IGNORED_EXCEPTIONS = [
        RestartWorkerException::class,
        RejectRedeliveredMessageException::class,
    ];

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

    protected function write(array $record): void
    {
        // Ignore certain exceptions
        if (isset($record['context']['exception'])
            && in_array($record['context']['exception']::class, self::IGNORED_EXCEPTIONS, true)) {
            return;
        }

        $event = Event::createEvent();
        $event->setLevel($this->getSeverityFromLevel($record['level']));
        $event->setMessage((new LineFormatter('%channel%.%level_name%: %message%'))->format($record));
        $event->setLogger(sprintf('monolog.%s', $record['channel']));

        $hint = new EventHint();
        if (isset($record['context']['exception']) && $record['context']['exception'] instanceof \Throwable) {
            $hint->exception = $record['context']['exception'];
        }

        $this->hub->withScope(function (Scope $scope) use ($record, $event, $hint): void {
            $scope->setExtra('monolog.channel', $record['channel']);
            $scope->setExtra('monolog.level', $record['level_name']);

            foreach ($this->breadcrumbsBuffer as $breadcrumbRecord) {
                $scope->addBreadcrumb(new Breadcrumb(
                    $this->getBreadcrumbLevelFromLevel($breadcrumbRecord['level']),
                    $this->getBreadcrumbTypeFromLevel($breadcrumbRecord['level']),
                    $breadcrumbRecord['channel'] ?? 'N/A',
                    $breadcrumbRecord['formatted'] ?? 'N/A'
                ));
            }

            $this->hub->captureEvent($event, $hint);
        });
    }

    /**
     * Translates the Monolog level into the Sentry severity level.
     */
    private function getSeverityFromLevel(int $level): Severity
    {
        return match ($level) {
            Logger::DEBUG => Severity::debug(),
            Logger::INFO, Logger::NOTICE => Severity::info(),
            Logger::WARNING => Severity::warning(),
            Logger::ERROR => Severity::error(),
            default => Severity::fatal(),
        };
    }

    /**
     * Translates the Monolog level into the Sentry breadcrumb level.
     */
    private function getBreadcrumbLevelFromLevel(int $level): string
    {
        return match ($level) {
            Logger::DEBUG => Breadcrumb::LEVEL_DEBUG,
            Logger::INFO, Logger::NOTICE => Breadcrumb::LEVEL_INFO,
            Logger::WARNING => Breadcrumb::LEVEL_WARNING,
            Logger::ERROR => Breadcrumb::LEVEL_ERROR,
            default => Breadcrumb::LEVEL_FATAL,
        };
    }

    /**
     * Translates the Monolog level into the Sentry breadcrumb type.
     */
    private function getBreadcrumbTypeFromLevel(int $level): string
    {
        return $level >= Logger::ERROR ? Breadcrumb::TYPE_ERROR : Breadcrumb::TYPE_DEFAULT;
    }
}
