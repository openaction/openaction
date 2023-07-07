<?php

namespace App\Community\History\Model;

class ContactHistoryItem
{
    private \DateTime $date;
    private string $type;
    private object $entity;

    public function __construct(\DateTime $date, string $type, object $entity)
    {
        $this->date = $date;
        $this->type = $type;
        $this->entity = $entity;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getEntity(): object
    {
        return $this->entity;
    }
}
