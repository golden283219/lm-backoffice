<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "shows_notifications".
 *
 * @property int $id
 * @property int $user_id
 * @property int $show_id
 */
class ShowsNotifications extends \yii\db\ActiveRecord
{
    public $email;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shows_notifications';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'show_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'show_id' => 'Show ID',
        ];
    }
}
