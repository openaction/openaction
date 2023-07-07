<?php

require __DIR__.'/autoload.php';

use Analytics\PageViewHandler;
use Analytics\PageViewPersister;
use Symfony\Component\HttpFoundation\Request;

$startTime = hrtime(true);

$dbUrl = $_SERVER['DATABASE_URL'] ?? 'postgres://main:main@database:5432/main?sslmode=disable&charset=utf8';

$handler = new PageViewHandler(new PageViewPersister($dbUrl));

$response = $handler->handle(Request::createFromGlobals());
$response->headers->set('X-App-Time', round((hrtime(true) - $startTime) / 1_000_000));
$response->send();
