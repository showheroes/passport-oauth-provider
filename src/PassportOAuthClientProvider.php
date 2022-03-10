<?php

namespace ShowHeroes\PassportOAuthProvider;

use Laravel\Socialite\Two\User;
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
        return config('services.passport.oauth_server') . '/oauth/token';
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
    protected function getTokenFields($code): array
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
                config('services.passport.oauth_server') . '/api/users/current',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Accept' => 'application/json',
                    ],
                ]
            );

        return json_decode($response->getBody(), true)['data'] ?? [];
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id'       => $user['id'],
            'nickname' => $user['name'],
            'email'    => $user['email'],
            'name'     => $user['name'],
            'avatar'   => $user['avatar'],
        ]);
    }
}
