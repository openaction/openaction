<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function handle(Request $request, int $type = HttpKernelInterface::MAIN_REQUEST, bool $catch = true): Response
    {
        $startTime = hrtime(true);

        if ($response = parent::handle($request, $type, $catch)) {
            $response->headers->set('X-App-Route', $request->attributes->get('_route'));
            $response->headers->set('X-App-Time', round((hrtime(true) - $startTime) / 1_000_000));
        }

        return $response;
    }
}
