<?php

use App\Kernel;

if (str_starts_with($_SERVER['REQUEST_URI'], '/projects/event')) {
    require __DIR__.'/../analytics/index.php';
    exit;
}

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
