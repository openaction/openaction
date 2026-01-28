<?php

namespace App\Tests\Messenger\Transport;

use App\Messenger\Transport\RetrySafeSerializer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Messenger\Stamp\ErrorDetailsStamp;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class RetrySafeSerializerTest extends TestCase
{
    public function testEncodeDropsErrorDetailsStamp(): void
    {
        $innerSerializer = new class implements SerializerInterface {
            public function encode(Envelope $envelope): array
            {
                $headers = [];

                foreach ($envelope->all() as $stampClass => $stamps) {
                    $headers['X-Message-Stamp-'.$stampClass] = 'encoded';
                }

                return [
                    'body' => 'encoded-body',
                    'headers' => $headers,
                ];
            }

            public function decode(array $encodedEnvelope): Envelope
            {
                return new Envelope(new \stdClass());
            }
        };

        $serializer = new RetrySafeSerializer($innerSerializer);

        $envelope = new Envelope(new \stdClass(), [
            ErrorDetailsStamp::create(new \RuntimeException('Boom')),
            new DelayStamp(1000),
        ]);

        $encoded = $serializer->encode($envelope);

        $this->assertArrayHasKey('headers', $encoded);
        $this->assertArrayNotHasKey('X-Message-Stamp-'.ErrorDetailsStamp::class, $encoded['headers']);
        $this->assertArrayHasKey('X-Message-Stamp-'.DelayStamp::class, $encoded['headers']);
    }
}
