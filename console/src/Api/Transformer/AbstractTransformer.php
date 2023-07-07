<?php

namespace App\Api\Transformer;

use League\Fractal\TransformerAbstract;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class AbstractTransformer extends TransformerAbstract
{
    protected UrlGeneratorInterface $urlGenerator;

    public static function describeResourceName(): string
    {
        return '';
    }

    public static function describeResourceSchema(): array
    {
        return [];
    }

    protected function createLink(string $route, array $params = [], bool $networkPath = false): string
    {
        return $this->urlGenerator->generate(
            $route,
            $params,
            $networkPath ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setUrlGenerator(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }
}
