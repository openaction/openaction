<?php

namespace App\Bridge\Mollie;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class MollieConnect implements MollieConnectInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $mollieConnectClientId,
        private readonly string $mollieConnectClientSecret,
        private readonly string $mollieConnectRedirectUri,
    ) {
    }

    public function getAuthorizationUrl(string $state, array $scopes): string
    {
        $params = [
            'client_id' => $this->mollieConnectClientId,
            'redirect_uri' => $this->mollieConnectRedirectUri,
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
    public function exchangeCodeForTokens(string $code): array
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
                'redirect_uri' => $this->mollieConnectRedirectUri,
            ]),
        ]);

        $data = $response->toArray(false);

        if (!isset($data['access_token'], $data['refresh_token'])) {
            $status = $response->getStatusCode();
            $error = is_array($data) ? ($data['error'] ?? 'unknown_error') : 'invalid_response';
            $description = is_array($data) ? ($data['error_description'] ?? 'No description provided') : 'Non-JSON response';
            throw new \RuntimeException(sprintf('Mollie token exchange failed (HTTP %d): %s - %s. Check that MOLLIE_CONNECT_REDIRECT_URI exactly matches your Mollie app redirect URL.', $status, (string) $error, (string) $description));
        }

        return $data;
    }

    /**
     * @return array{access_token:string, refresh_token:string, expires_in?:int, token_type?:string, scope?:string}
     */
    public function refreshAccessToken(string $refreshToken): array
    {
        $basic = base64_encode($this->mollieConnectClientId.':'.$this->mollieConnectClientSecret);

        $response = $this->httpClient->request('POST', 'https://api.mollie.com/oauth2/tokens', [
            'headers' => [
                'Authorization' => 'Basic '.$basic,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => http_build_query([
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                // Required if redirect_uri was included initially
                'redirect_uri' => $this->mollieConnectRedirectUri,
            ]),
        ]);

        $data = $response->toArray(false);

        if (!isset($data['access_token'], $data['refresh_token'])) {
            $status = $response->getStatusCode();
            $error = is_array($data) ? ($data['error'] ?? 'unknown_error') : 'invalid_response';
            $description = is_array($data) ? ($data['error_description'] ?? 'No description provided') : 'Non-JSON response';
            throw new \RuntimeException(sprintf('Mollie token refresh failed (HTTP %d): %s - %s.', $status, (string) $error, (string) $description));
        }

        return $data;
    }
}
