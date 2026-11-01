<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Services;

use Eamirgh\Basalam\DTOs\TokenData;
use Eamirgh\Basalam\DTOs\UserData;
use Eamirgh\Basalam\Http\ApiClient;

class AuthService
{
    public function __construct(
        protected ApiClient $client,
        protected ?string $clientId = null,
        protected ?string $clientSecret = null,
        protected ?string $redirectUri = null,
        protected string $ssoUrl = 'https://basalam.com/accounts/sso',
    ) {
    }

    /**
     * Generate SSO authorization URL for OAuth 2.0 Authorization Code flow
     *
     * @param array<int, string> $scopes
     */
    public function getAuthorizationUrl(
        ?string $redirectUri = null,
        array $scopes = [],
        ?string $state = null,
    ): string {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $redirectUri ?? $this->redirectUri,
            'scope' => implode(' ', $scopes),
        ];

        if ($state !== null) {
            $params['state'] = $state;
        }

        return $this->ssoUrl . '?' . http_build_query($params);
    }

    /**
     * Exchange authorization code for access and refresh tokens
     */
    public function exchangeCode(string $code, ?string $redirectUri = null): TokenData
    {
        $response = $this->client->post('/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $redirectUri ?? $this->redirectUri,
            'code' => $code,
        ]);

        return TokenData::fromArray($response);
    }

    /**
     * Refresh an expired access token using refresh_token
     */
    public function refreshToken(string $refreshToken): TokenData
    {
        $response = $this->client->post('/oauth/token', [
            'grant_type' => 'refresh_token',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $refreshToken,
        ]);

        return TokenData::fromArray($response);
    }

    /**
     * Client credentials grant for machine-to-machine APIs (e.g. Wallet)
     *
     * @param array<int, string> $scopes
     */
    public function clientCredentials(array $scopes = []): TokenData
    {
        $payload = [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ];

        if (! empty($scopes)) {
            $payload['scope'] = implode(' ', $scopes);
        }

        $response = $this->client->post('/oauth/token', $payload);

        return TokenData::fromArray($response);
    }

    /**
     * Validate current token and inspect identity
     */
    public function whoami(): UserData
    {
        $response = $this->client->get('/whoami');

        return UserData::fromArray($response);
    }
}
