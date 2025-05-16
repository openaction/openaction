<?php

namespace App\Maintenance;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ActiveMaintenanceException extends NotFoundHttpException
{
    public function __construct()
    {
        parent::__construct('Citipo is in maintenance.');
    }
}
