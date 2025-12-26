<?php

use App\Kernel;

if (isset($_SERVER['HTTP_CF_VISITOR'])) {
    $cfVisitor = json_decode($_SERVER['HTTP_CF_VISITOR'], true);

    if (is_array($cfVisitor) && ($cfVisitor['scheme'] ?? null) === 'https') {
        // Force HTTPS semantics for downstream
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
        $_SERVER['HTTP_X_FORWARDED_PORT'] = '443';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '127.0.0.1';
    }
}

if (str_starts_with($_SERVER['REQUEST_URI'], '/projects/event')) {
    require __DIR__.'/../analytics/index.php';
    exit;
}

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
