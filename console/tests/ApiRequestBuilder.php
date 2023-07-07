<?php

namespace App\Tests;

use App\Util\Json;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class ApiRequestBuilder
{
    public const TOKEN_DEFAULT = '41d7821176ed9079640650922e1290aba97b949362339a7ed5539f0d5b9f21ba';
    public const TOKEN_CITIPO = '748ea240b01970d6c9de708de7602e613adb4dd02aa084435088c8c5f806d9ad';
    public const TOKEN_ACME = '31cf08f5e0354198a3b26b5b08f59a4ed871cbaec6e4eb8b158fab57a7193b7a';

    private KernelBrowser $client;
    private string $method;
    private string $endpoint;
    private ?string $token = self::TOKEN_DEFAULT;
    private ?string $authToken = null;
    private ?string $content = '';
    private array $files = [];
    private array $parameters = [];
    private array $server = [];

    public function __construct(KernelBrowser $client, string $method, string $endpoint)
    {
        $this->client = $client;
        $this->method = $method;
        $this->endpoint = $endpoint;
    }

    public function withApiToken(?string $token): self
    {
        $clone = clone $this;
        $clone->token = $token;

        return $clone;
    }

    public function withAuthToken(string $authToken): self
    {
        $clone = clone $this;
        $clone->authToken = $authToken;

        return $clone;
    }

    public function withFile(string $fieldName, UploadedFile $file): self
    {
        $clone = clone $this;
        $clone->files[$fieldName] = $file;

        return $clone;
    }

    public function withServer(string $name, string $value): self
    {
        $clone = clone $this;
        $clone->server[$name] = $value;

        return $clone;
    }

    public function withContent(string $content): self
    {
        $clone = clone $this;
        $clone->content = $content;

        return $clone;
    }

    public function withParameters(array $parameters): self
    {
        $clone = clone $this;
        $clone->parameters = $parameters;

        return $clone;
    }

    public function toArray(): array
    {
        try {
            return Json::decode($this->send()->getContent());
        } catch (\Exception) {
            return [];
        }
    }

    public function send(): Response
    {
        $server = array_merge(['HTTP_ACCEPT' => 'application/ld+json'], $this->server);

        if ($this->token) {
            $server['HTTP_AUTHORIZATION'] = 'Bearer '.$this->token;
        }

        if ($this->authToken) {
            $server['HTTP_X_CITIPO_AUTH_TOKEN'] = $this->authToken;
        }

        $this->client->request($this->method, $this->endpoint, $this->parameters, $this->files, $server, $this->content);

        return $this->client->getResponse();
    }
}
