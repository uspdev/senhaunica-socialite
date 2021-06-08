<?php

namespace Uspdev\SenhaunicaSocialite;

use Illuminate\Support\Facades\Storage;
use League\OAuth1\Client\Credentials\TemporaryCredentials;
use League\OAuth1\Client\Credentials\TokenCredentials;
use SocialiteProviders\Manager\OAuth1\Server as BaseServer;
use SocialiteProviders\Manager\OAuth1\User;

class Server extends BaseServer
{   
    protected $base_url_usp = 'https://uspdigital.usp.br/wsusuario/oauth';
    public function baseUrlUsp() {
        if (config('senhaunica.dev') != "no") {
            $this->base_url_usp = config('senhaunica.dev');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function urlTemporaryCredentials()
    {
        $this->baseUrlUsp();
        return "{$this->base_url_usp}/request_token";
    }

    /**
     * {@inheritDoc}
     */
    public function urlAuthorization()
    {
        $this->baseUrlUsp();
        return "{$this->base_url_usp}/authorize";
    }

    /**
     * {@inheritDoc}
     */
    public function urlTokenCredentials()
    {
        $this->baseUrlUsp();
        return "{$this->base_url_usp}/access_token";
    }

    /**
     * {@inheritDoc}
     */
    public function urlUserDetails()
    {
        $this->baseUrlUsp();
        return "{$this->base_url_usp}/usuariousp";
    }

    /**
     * {@inheritDoc}
     */
    public function userDetails($data, TokenCredentials $tokenCredentials)
    {
        if (config('senhaunica.debug') == true) {
            Storage::put("debug/oauth/".$data['loginUsuario'].".json", json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }

        $user                      = new User();
        $user->codpes              = $data['loginUsuario'];
        $user->nompes              = $data['nomeUsuario'];
        $user->email               = $data['emailPrincipalUsuario'];
        $user->emailUsp            = $data['emailUspUsuario'];
        $user->emailAlternativo    = $data['emailAlternativoUsuario'];
        $user->telefone            = $data['numeroTelefoneFormatado'];
        /*
        *    Dentro do Vinculo terão as seguintes informações
        *    'tipoVinculo'
        *    'codigoSetor'
        *    'nomeAbreviadoSetor'
        *    'nomeSetor'
        *    'codigoUnidade'
        *    'siglaUnidade'
        *    'nomeUnidade'
        *    'nomeAbreviadoFuncao'
        */
        if ($data['tipoUsuario'] == 'I'){
            $user->vinculo = $data['vinculo'];
        } else {
            $user->vinculo = array();
        }

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function userUid($data, TokenCredentials $tokenCredentials)
    {
        //return $data['id'];
    }

    /**
     * {@inheritDoc}
     */
    public function userEmail($data, TokenCredentials $tokenCredentials)
    {
        //return $data['email'];
    }

    /**
     * {@inheritDoc}
     */
    public function userScreenName($data, TokenCredentials $tokenCredentials)
    {
        //return $data['screen_name'];
    }
    
    // removing $data['oauth_callback_confirmed'] from createTemporaryCredentials
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
    
    // adding callback_id parameter
    public function getAuthorizationUrl($temporaryIdentifier, array $options = [])
    {
        if ($temporaryIdentifier instanceof TemporaryCredentials) {
            $temporaryIdentifier = $temporaryIdentifier->getIdentifier();
        }

        $parameters = array('oauth_token' => $temporaryIdentifier,'callback_id' => config('senhaunica.callback_id'));

        $url = $this->urlAuthorization();
        $queryString = http_build_query($parameters);

        return $this->buildUrl($url, $queryString);
    }
    
    // adding oauth_verifier to $uri
    public function getTokenCredentials(TemporaryCredentials $temporaryCredentials, $temporaryIdentifier, $verifier)
    {
        if ($temporaryIdentifier !== $temporaryCredentials->getIdentifier()) {
            throw new \InvalidArgumentException(
                'Temporary identifier passed back by server does not match that of stored temporary credentials.
                Potential man-in-the-middle.'
            );
        }
        $uri = $this->urlTokenCredentials();
        $bodyParameters = array('oauth_verifier' => $verifier);

        $uri = $this->urlTokenCredentials().'?oauth_verifier='.$verifier;
        $bodyParameters = ['oauth_verifier' => $verifier, 'oauth_token' => $temporaryIdentifier];

        $client = $this->createHttpClient();
        $headers = $this->getHeaders($temporaryCredentials, 'POST', $uri, $bodyParameters);

        try {
            $response = $client->post($uri, [
                'headers' => $headers,
                'form_params' => $bodyParameters,
            ]);
        } catch (BadResponseException $e) {
            return $this->handleTokenCredentialsBadResponse($e);
        }

       return $this->createTokenCredentials((string) $response->getBody());
       
    }
    
    // change request from 'get' to 'post'
    protected function fetchUserDetails(TokenCredentials $tokenCredentials, $force = true)
    {
        
        if (!$this->cachedUserDetailsResponse || $force) {
            $url = $this->urlUserDetails();
            $client = $this->createHttpClient();
            $headers = $this->getHeaders($tokenCredentials, 'POST', $url);

            try {
                $response = $client->post($url, [
                    'headers' => $headers,
                ]);
            } catch (BadResponseException $e) {
                $response = $e->getResponse();
                $body = $response->getBody();
                $statusCode = $response->getStatusCode();

                throw new \Exception(
                    "Received error [$body] with status code [$statusCode] when retrieving token credentials."
                );
            }
            switch ($this->responseType) {
                case 'json':
                    $this->cachedUserDetailsResponse = json_decode((string) $response->getBody(), true);
                    break;

                case 'xml':
                    $this->cachedUserDetailsResponse = simplexml_load_string((string) $response->getBody());
                    break;

                case 'string':
                    parse_str((string) $response->getBody(), $this->cachedUserDetailsResponse);
                    break;

                default:
                    throw new \InvalidArgumentException("Invalid response type [{$this->responseType}].");
            }
        }

        return $this->cachedUserDetailsResponse;
    }
}
