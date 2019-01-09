<?php

namespace Uspdev\SenhaunicaSocialite;

use SocialiteProviders\Manager\OAuth1\AbstractProvider;
use SocialiteProviders\Manager\OAuth1\User;

class Provider extends AbstractProvider
{
    /**
     * Unique Provider Identifier.
     */
    const IDENTIFIER = 'SENHAUNICA';

    public function user()
    {
        if (! $this->hasNecessaryVerifier()) {
            throw new \InvalidArgumentException("Invalid request. Missing OAuth verifier.");
        }

        $user = $this->server->getUserDetails($token = $this->getToken());

        return (new User())->map([
            'codpes'              => $user->codpes,
            'nompes'              => $user->nompes,
            'email'               => $user->email,
            'emailUsp'            => $user->emailUsp,
            'emailAlternativo'    => $user->emailAlternativo,
            'telefone'            => $user->telefone,
            'vinculo'             => $user->vinculo,
        ])->setToken($token->getIdentifier(), $token->getSecret());

    }
}
