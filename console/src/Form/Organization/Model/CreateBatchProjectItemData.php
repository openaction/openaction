<?php

namespace App\Form\Organization\Model;

use App\Util\Json;
use Symfony\Component\Validator\Constraints as Assert;

class CreateBatchProjectItemData
{
    #[Assert\Length(max: 60)]
    public ?string $name = null;

    #[Assert\Email]
    public ?string $adminEmail = null;

    #[Assert\Choice(['global', 'local'])]
    public ?string $type = 'global';

    public ?string $areasIds = null;

    public function parseAreasIds(): array
    {
        if (!$this->areasIds) {
            return [];
        }

        try {
            return array_keys(Json::decode($this->areasIds));
        } catch (\Throwable) {
            return [];
        }
    }
}
