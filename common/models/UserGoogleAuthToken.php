<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * UserGoogleAuthToken model
 *
 * @property integer $user_id
 * 
 */
class UserGoogleAuthToken extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_google_auth_token';
    }

    /**
     * {@inheritdoc}
     */
    public static function getUserTokenRecord($user_id)
    {
        return static::findOne(['user_id' => $user_id]);
    }

}
