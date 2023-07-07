<?php

namespace App\Bridge\Revue;

interface RevueInterface
{
    public function getSubscribers(string $apiToken): array;
}
