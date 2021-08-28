<?php

namespace frontend\services\google;

use Google_Client;
use Yii;

/**
 * Google API Service
 */
class GoogleAPI
{
    /** @var Google_Client $client */
    private $client;

    /** @var boolean $authorized */
    private $authorized;

    /** @var string $tokenSessionName */
    private $tokenSessionName;

    /** @var string $credintionalsPath */
    private $credintionalsPath;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->authorized = true;
        $this->tokenSessionName = defined('GOOGLE_TOKEN_SESSION_NAME') ?
                                    GOOGLE_TOKEN_SESSION_NAME : 'google_api_token';
        $this->credintionalsPath = file_exists(__DIR__ . '/files/credentials.json') ?
                                    __DIR__ . '/files/credentials.json' : null;
    }

    /**
     * Returns an authorized API client.
     * @return Google_Client the authorized client object
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Check if the user need to authorize the access. 
     * @return boolean indicate if an authorization is needed or not
     */
    public function needAuthorization()
    {
        return !$this->authorized;
    }

    /**
     * Get the authorization url from google.
     * @return string fully qualified authorization url from google
     * @throws Exception
     */
    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    /**
     * Set options
     * Set the token in the request
     * Check and refresh token if expired
     */
    public function initSetAndRefreshToken($scope)
    {
        $this->setOptions($scope);
        $this->setToken();
        $this->refreshTokenIfExpired();
    }

    /**
     * Set options
     * Get a token from google
     * Save the token in session
     */
    public function initGetAndSaveToken($scope, $code)
    {
        $this->setOptions($scope);
        $this->saveToken($this->getToken($code));
    }

    /**
     * Set google api options.
     */
    private function setOptions($scope)
    {
        $this->client->setApplicationName(Yii::$app->name);
        $this->client->setScopes($scope);
        $this->client->setAuthConfig($this->credintionalsPath);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');
        $this->client->setIncludeGrantedScopes(true);
    }

    /**
     * Get token after authorization. 
     * @return array|mixed the google token
     * @throws Exception
     */
    private function getToken($code)
    {
        return $this->client->fetchAccessTokenWithAuthCode($code);
    }

    /**
     * Get token after authorization. 
     */
    private function saveToken($token)
    {
        Yii::$app->session[$this->tokenSessionName] = json_encode($token);
    }

    /**
     * Set token to be used in the requests. 
     */
    private function setToken()
    {
        $token = Yii::$app->session->get($this->tokenSessionName);
        if ($token) {
            $this->client->setAccessToken(
                json_decode(
                    $token,
                    true
                )
            );
        }
    }

    /**
     * Check if token is expired and refresh. 
     * @throws Exception
     */
    private function refreshTokenIfExpired()
    {
        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            } else {
                $this->authorized = false;
            }
        }
    }
}
