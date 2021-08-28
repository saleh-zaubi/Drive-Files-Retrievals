<?php

namespace frontend\controllers;

use yii\web\Controller;
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
    private $scope;

    public function __construct($id, $module, $config = [], GoogleAPI $googleApi)
    {
        parent::__construct($id, $module, $config);
        $this->googleAPI = $googleApi;
        $this->scope = Google_Service_Drive::DRIVE_METADATA_READONLY;
    }

    public function actionFilesList()
    {
        // Yii::$app->session->remove(GOOGLE_TOKEN_SESSION_NAME);
        try {
            $this->googleAPI->initSetAndRefreshToken($this->scope);
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
                $this->googleAPI->initGetAndSaveToken($this->scope, $_GET['code']);
            } catch (Exception $e) {
                return $this->handleError($e, 'Authorization Failed', 'Failed to get token from google');
            }
        }
        return $this->redirect(['drive/files-list']);
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
