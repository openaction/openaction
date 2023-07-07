<?php

namespace App\Bridge\Meilisearch\Model;

class Task
{
    public function __construct(private int $uid, private string $status)
    {
    }

    public function getUid(): int
    {
        return $this->uid;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
