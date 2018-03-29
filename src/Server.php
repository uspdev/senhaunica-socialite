<?php

//namespace SocialiteProviders\Senhaunica;
namespace Uspdev\SenhaunicaSocialite;

use League\OAuth1\Client\Credentials\TokenCredentials;
use SocialiteProviders\Manager\OAuth1\Server as BaseServer;
use SocialiteProviders\Manager\OAuth1\User;

//use League\OAuth1\Client\Credentials\CredentialsException;
use League\OAuth1\Client\Credentials\TemporaryCredentials;

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
        $user->id       = $data['loginUsuario'];
        $user->name     = $data['nomeUsuario'];
        $user->email    = $data['emailPrincipalUsuario'];

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
    public function getAuthorizationUrl($temporaryIdentifier)
    {
        if ($temporaryIdentifier instanceof TemporaryCredentials) {
            $temporaryIdentifier = $temporaryIdentifier->getIdentifier();
        }

        $parameters = array('oauth_token' => $temporaryIdentifier,'callback_id' => env('SENHAUNICA_CALLBACK_ID'));

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
