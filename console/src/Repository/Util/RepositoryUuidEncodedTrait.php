<?php

namespace App\Repository\Util;

use App\Entity\Project;
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

    public function findOneByBase62UidOrSlug(Project $project, string $base62UidOrSlug)
    {
        if (($uuid = Uid::fromBase62($base62UidOrSlug))
            && ($entity = $this->findOneBy(['uuid' => $uuid->toRfc4122()]))) {
            return $entity;
        }

        return $this->findOneBy(['project' => $project, 'slug' => $base62UidOrSlug]);
    }
}
