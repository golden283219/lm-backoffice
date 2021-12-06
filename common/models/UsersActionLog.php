<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "users_action_log".
 *
 * @property int $id
 * @property int $id_user
 * @property string $action
 * @property string $category
 * @property string $data
 * @property int $log_time
 */
class UsersActionLog extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users_action_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_user', 'log_time'], 'required'],
            [['id_user', 'log_time'], 'integer'],
            [['data'], 'string'],
            [['action', 'category'], 'string', 'max' => 80],
        ];
    }

    public static function GetLogCategories () {
        return [
            'MoviesReports' => 'Movies Reports',
            'Drafts' => 'Drafts'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_user' => 'Id User',
            'action' => 'Action',
            'category' => 'Category',
            'data' => 'Data',
            'log_time' => 'Log Time',
        ];
    }
}
