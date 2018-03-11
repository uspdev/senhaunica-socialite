<?php

namespace SocialiteProviders\Senhaunica;

use League\OAuth1\Client\Credentials\TokenCredentials;
use SocialiteProviders\Manager\OAuth1\Server as BaseServer;
use SocialiteProviders\Manager\OAuth1\User;

class Server extends BaseServer
{
    /**
     * {@inheritDoc}
     */
    public function urlTemporaryCredentials()
    {
        return 'https://uspdigital.usp.br/wsusuario/oauth/request_token';
    }

    /**
     * {@inheritDoc}
     */
    public function urlAuthorization()
    {
        return 'https://uspdigital.usp.br/wsusuario/oauth/authorize';
    }

    /**
     * {@inheritDoc}
     */
    public function urlTokenCredentials()
    {
        return 'https://uspdigital.usp.br/wsusuario/oauth/access_token';
    }

    /**
     * {@inheritDoc}
     */
    public function urlUserDetails()
    {
        return 'https://uspdigital.usp.br/wsusuario/oauth/usuariousp';
    }

    /**
     * {@inheritDoc}
     */
    public function userDetails($data, TokenCredentials $tokenCredentials)
    {
        $user           = new User();
        $user->id       = $data['id'];
        $user->nickname = $data['nickname'];
        $user->name     = $data['name'];
        $user->email    = $data['email'];
        $user->avatar   = $data['avatar'];

        $used = ['id', 'nickname', 'name', 'email', 'avatar'];

        foreach ($data as $key => $value) {
            if (!in_array($key, $used)) {
                $used[] = $key;
            }
        }

        $user->extra = array_diff_key($data, array_flip($used));

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function userUid($data, TokenCredentials $tokenCredentials)
    {
        return $data['id'];
    }

    /**
     * {@inheritDoc}
     */
    public function userEmail($data, TokenCredentials $tokenCredentials)
    {
        return $data['email'];
    }

    /**
     * {@inheritDoc}
     */
    public function userScreenName($data, TokenCredentials $tokenCredentials)
    {
        return $data['screen_name'];
    }
    
    protected function createTemporaryCredentials($body)
    {
        parse_str($body, $data);
        if (!$data || !is_array($data)) {
            throw new CredentialsException('Unable to parse temporary credentials response.');
        }
        $temporaryCredentials = new TemporaryCredentials();
        $temporaryCredentials->setIdentifier($data['oauth_token']);
        $temporaryCredentials->setSecret($data['oauth_token_secret']);
        return $temporaryCredentials;
    }
}
