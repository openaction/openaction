<?php

namespace App\Messenger\Transport;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\ErrorDetailsStamp;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

final class RetrySafeSerializer implements SerializerInterface
{
    public function __construct(private readonly SerializerInterface $decorated)
    {
    }

    public function encode(Envelope $envelope): array
    {
        if (null !== $envelope->last(ErrorDetailsStamp::class)) {
            $envelope = $envelope->withoutAll(ErrorDetailsStamp::class);
        }

        return $this->decorated->encode($envelope);
    }

    public function decode(array $encodedEnvelope): Envelope
    {
        return $this->decorated->decode($encodedEnvelope);
    }
}
