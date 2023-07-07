<?php

namespace App\Bridge\Unlayer;

interface UnlayerInterface
{
    public function getEmailingTemplates(): array;

    public function getAutomationTemplates(): array;
}
