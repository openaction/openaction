<?php

namespace App\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ApiRequestQueryParser
{
    private RequestStack $stack;

    public function __construct(RequestStack $stack)
    {
        $this->stack = $stack;
    }

    public function getIncludes(): string
    {
        return $this->getRequest()->query->get('includes', '');
    }

    public function getExcludes(): string
    {
        return $this->getRequest()->query->get('excludes', '');
    }

    public function getPage(): int
    {
        return $this->getRequest()->query->getInt('page', 1);
    }

    private function getRequest(): Request
    {
        if ($request = $this->stack->getCurrentRequest()) {
            return $request;
        }

        throw new \LogicException('Current request unavailable');
    }
}
