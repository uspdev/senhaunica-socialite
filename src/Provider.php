<?php

namespace SocialiteProviders\Senhaunica;

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
            'id'       => $user->id,
            'name'     => $user->name,
            'email'    => $user->email,
            
            'tipoVinculo'         => $user->tipoVinculo,
            'codigoSetor'         => $user->codigoSetor, 
            'nomeAbreviadoSetor'  => $user->nomeAbreviadoSetor,
            'nomeSetor'           => $user->nomeSetor,
            'codigoUnidade'       => $user->codigoUnidade,
            'siglaUnidade'        => $user->siglaUnidade, 
            'nomeUnidade'         => $user->nomeUnidade,
            'nomeAbreviadoFuncao' => $user->nomeAbreviadoFuncao,
            'vinculosAdicionais'  => $user->vinculosAdicionais,
            
        ])->setToken($token->getIdentifier(), $token->getSecret());

    }
}
