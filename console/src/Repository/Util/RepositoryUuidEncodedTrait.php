<?php

namespace App\Repository\Util;

use App\Util\Uid;

trait RepositoryUuidEncodedTrait
{
    abstract public function findOneBy(array $criteria, array $orderBy = null);

    public function findOneByBase62Uid(string $base62Uid)
    {
        if (!$uuid = Uid::fromBase62($base62Uid)) {
            return null;
        }

        return $this->findOneBy(['uuid' => $uuid->toRfc4122()]);
    }
}
