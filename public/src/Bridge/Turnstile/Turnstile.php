<?php

namespace App\Bridge\Turnstile;

use App\Bridge\Turnstile\Model\CaptchaChallenge;
use App\Client\Model\ApiResource;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Turnstile
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    public function createCaptchaChallenge(?ApiResource $project): ?CaptchaChallenge
    {
        if (!$project || !$project->captchaSiteKey || !$project->captchaSecretKey) {
            return null;
        }

        return new CaptchaChallenge(
            httpClient: $this->httpClient,
            siteKey: $project->captchaSiteKey,
            secretKey: $project->captchaSecretKey,
        );
    }
}
