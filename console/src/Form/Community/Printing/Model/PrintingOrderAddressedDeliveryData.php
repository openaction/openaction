<?php

namespace App\Form\Community\Printing\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class PrintingOrderAddressedDeliveryData
{
    #[Assert\NotBlank]
    #[Assert\File(maxSize: '20Mi', mimeTypes: ['application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])]
    public ?UploadedFile $addressList = null;
}
