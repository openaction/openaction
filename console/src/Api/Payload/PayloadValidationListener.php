<?php

declare(strict_types=1);

namespace App\Api\Payload;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

final class PayloadValidationListener implements EventSubscriberInterface
{
    public function __construct(private readonly SerializerInterface $serializer)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onException',
        ];
    }

    public function onException(ExceptionEvent $event): void
    {
        if (!$event->getThrowable() instanceof PayloadValidationException) {
            return;
        }

        /** @var PayloadValidationException $exception */
        $exception = $event->getThrowable();

        $event->setResponse(
            new JsonResponse(
                data: $this->serializer->serialize($exception->getConstraintViolationList(), 'json'),
                status: Response::HTTP_BAD_REQUEST,
                json: true,
            )
        );
    }
}
