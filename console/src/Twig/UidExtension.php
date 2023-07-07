<?php

namespace App\Twig;

use App\Util\Uid;
use Symfony\Component\Uid\Uuid;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Webmozart\Assert\Assert;

class UidExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('toBase62', [$this, 'toBase62']),
        ];
    }

    public function toBase62($uuid): string
    {
        if (is_string($uuid)) {
            $uuid = Uuid::fromString($uuid);
        }

        Assert::isInstanceOf($uuid, Uuid::class);

        return Uid::toBase62($uuid);
    }
}
