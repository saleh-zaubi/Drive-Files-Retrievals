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

    /** @var string|null $credintionalsPath */
    private $credintionalsPath;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->authorized = true;
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
     * Set google api options.
     */
    public function setOptions($scope)
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
    public function getToken($code)
    {
        return $this->client->fetchAccessTokenWithAuthCode($code);
    }

    /**
     * Set token to be used in the requests. 
     */
    public function setToken($token)
    {
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
    public function refreshTokenIfExpired()
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
