<?php

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class SubscribeNewsletterData
{
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     * @Assert\Length(max=150)
     */
    public ?string $email = null;

    /**
     * @Assert\Length(max=150)
     */
    public ?string $firstName = null;

    /**
     * @Assert\Length(max=150)
     */
    public ?string $lastName = null;

    /**
     * @Assert\Length(max=50)
     */
    public ?string $phone = null;

    /**
     * @Assert\Country()
     */
    public ?string $country = 'FR';

    /**
     * @Assert\Length(min=2, max=30)
     */
    public ?string $zipCode = null;

    /**
     * @Assert\NotBlank()
     */
    public ?bool $acceptPolicy = false;
}
