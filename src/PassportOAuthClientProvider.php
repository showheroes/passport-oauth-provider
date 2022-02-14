<?php

namespace ShowHeroes\PassportOAuthProvider;

use Psr\Http\Message\StreamInterface;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;

/**
 * Class PassportOAuthClientProvider
 * @package ShowHeroes\PassportOAuthProvider
 * @method parseAccessToken(StreamInterface $getBody)
 */
class PassportOAuthClientProvider extends AbstractProvider implements ProviderInterface
{

    public const SOCIALITE_PROVIDER_NAME = 'passport';
    public const SCOPE_USER_INFO = 'user_info';

    /**
     * {}
     * @param $state
     * @return mixed
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(config('services.passport.oauth_server') . '/oauth/authorize', $state);
    }

    /**
     * {}
     */
    protected function getTokenUrl(): string
    {
        return confug('services.passport.oauth_server') . '/oauth/token';
    }

    /**
     * {}
     * @param $code
     * @return mixed
     */
    public function getAccessToken($code)
    {
        $response = $this->getHttpClient()->post(
            $this->getTokenUrl(),
            [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret)
                ],
                'body' => $this->getTokenFields($code),
            ]
        );

        return $this->parseAccessToken($response->getBody());
    }

    /**
     * Get the POST fields for the token request.
     *
     * @param string $code
     * @return array
     */
    protected function getTokenFields(string $code): array
    {
        return [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'redirect_uri' => $this->redirectUrl,
        ];
    }

    /**
     * {}
     * @param $token
     * @return array
     */
    protected function getUserByToken($token): array
    {
        $response = $this->getHttpClient()
            ->get(
                config('services.passport.oauth_server') . '/api/v1/oauth/user',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Accept' => 'application/json',
                    ],
                ]
            );

        return json_decode($response->getBody(), true)['data'] ?? [];
    }
}
