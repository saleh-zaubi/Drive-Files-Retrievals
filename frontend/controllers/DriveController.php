<?php

namespace frontend\controllers;

use yii\web\Controller;
use yii\filters\AccessControl;
use common\models\UserGoogleAuthToken;
use frontend\services\google\GoogleAPI;
use Google_Service_Drive;
use Exception;
use Yii;

/**
 * Google Drive Controller
 */
class DriveController extends Controller
{
    private $googleAPI;

    public function __construct($id, $module, $config = [], GoogleAPI $googleApi)
    {
        parent::__construct($id, $module, $config);
        $this->googleAPI = $googleApi;
        $this->googleAPI->setOptions(Google_Service_Drive::DRIVE_METADATA_READONLY);
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['files-list', 'auth-return-url'],
                'rules' => [
                    [
                        'actions' => ['files-list', 'auth-return-url'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ]
        ];
    }

    public function actionFilesList()
    {
        try {
            $this->googleAPI->setToken($this->getUserSavedToken());
            $this->googleAPI->refreshTokenIfExpired();
        } catch (Exception $e) {
            return $this->handleError($e, 'Authorization Failed', 'Invalid token');
        }

        if ($this->googleAPI->needAuthorization()) {
            return $this->handleAuthNeeded();
        }

        $service = new Google_Service_Drive($this->googleAPI->getClient());

        $files = $service->files->listFiles([
            'fields' => "nextPageToken,files(name,size,thumbnailLink,webContentLink,modifiedTime,owners)"
        ]);

        return $this->render('filesList', [
            'files' => $files
        ]);
    }

    public function actionAuthReturnUrl()
    {
        if (isset($_GET['code'])) {
            try {
                $token = $this->googleAPI->getToken($_GET['code']);
            } catch (Exception $e) {
                return $this->handleError($e, 'Authorization Failed', 'Failed to get token from google');
            }
            $this->saveUserToken($token);
        }
        return $this->redirect(['drive/files-list']);
    }

    private function getUserSavedToken()
    {
        $userTokenRecord = $this->getUserTokenRecord();
        if ($userTokenRecord) {
            return $userTokenRecord->token;
        }
        return null;
    }

    private function saveUserToken($token)
    {
        $userTokenRecord = $this->getUserTokenRecord();
        if (!$userTokenRecord) {
            $userTokenRecord = new UserGoogleAuthToken();
            $userTokenRecord->user_id = Yii::$app->user->identity->id;
            $userTokenRecord->created_at = time();
        }
        $userTokenRecord->updated_at = time();
        $userTokenRecord->token = json_encode($token);
        $userTokenRecord->save();
    }

    private function getUserTokenRecord()
    {
        return UserGoogleAuthToken::getUserTokenRecord(Yii::$app->user->identity->id);
    }

    private function handleAuthNeeded()
    {
        try {
            $authUrl = $this->googleAPI->getAuthUrl();
        } catch (Exception $e) {
            return $this->handleError($e, 'Need Authorization', 'Failed to get the Authorization url');
        }

        return $this->render('authorize', [
            'name' => 'Authorization is Required',
            'message' => 'You have to authorize the app to access your files list!',
            'authUrl' => $authUrl
        ]);
    }

    private function handleError($e, $name, $message)
    {
        Yii::error($e);
        return $this->render('error', [
            'name' => $name,
            'message' => $message
        ]);
    }
}
