<?php

namespace App\Messenger\Stamp;

use App\Util\Uid;
use Symfony\Component\Messenger\Stamp\StampInterface;

class UniqueIdStamp implements StampInterface
{
    private string $uniqueId;

    public function __construct()
    {
        $this->uniqueId = Uid::toBase62(Uid::random());
    }

    public function getUniqueId(): string
    {
        return $this->uniqueId;
    }
}
