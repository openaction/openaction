<?php

namespace App\Form\Website\Model;

use App\Entity\Website\PetitionLocalized;
use Symfony\Component\Validator\Constraints as Assert;

class PetitionLocalizedLocaleData
{
    public const LOCALES = [
        PetitionLocalized::LOCALE_EN,
        PetitionLocalized::LOCALE_FR,
        PetitionLocalized::LOCALE_DE,
        PetitionLocalized::LOCALE_IT,
        PetitionLocalized::LOCALE_NL,
        PetitionLocalized::LOCALE_PT,
    ];

    #[Assert\NotBlank]
    #[Assert\Choice(choices: self::LOCALES)]
    public ?string $locale = null;

    public ?string $petitionUuid = null;

    public function __construct(?string $petitionUuid = null)
    {
        $this->petitionUuid = $petitionUuid;
    }
}
