<?php

namespace App\Bridge\Mollie;

use Symfony\Component\HttpClient\HttpOptions;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MollieConnect
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $mollieConnectClientId,
        private readonly string $mollieConnectClientSecret,
    ) {
    }

    public function getAuthorizationUrl(string $redirectUri, string $state, array $scopes): string
    {
        $params = [
            'client_id' => $this->mollieConnectClientId,
            'redirect_uri' => $redirectUri,
            'state' => $state,
            'scope' => implode(' ', $scopes),
            'response_type' => 'code',
            'approval_prompt' => 'auto',
        ];

        return 'https://my.mollie.com/oauth2/authorize?'.http_build_query($params);
    }

    /**
     * @return array{access_token:string, refresh_token:string, expires_in?:int, token_type?:string, scope?:string}
     */
    public function exchangeCodeForTokens(string $code, string $redirectUri): array
    {
        $basic = base64_encode($this->mollieConnectClientId.':'.$this->mollieConnectClientSecret);

        $response = $this->httpClient->request('POST', 'https://api.mollie.com/oauth2/tokens', [
            'headers' => [
                'Authorization' => 'Basic '.$basic,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => http_build_query([
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $redirectUri,
            ]),
        ]);

        $data = $response->toArray(false);

        if (!isset($data['access_token'], $data['refresh_token'])) {
            throw new \RuntimeException('Invalid response from Mollie token endpoint.');
        }

        return $data;
    }
}

