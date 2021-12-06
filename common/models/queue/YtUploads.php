<?php

namespace common\models\queue;

use Yii;

/**
 * This is the model class for table "yt_uploads".
 *
 * @property string $key
 * @property string $yt_link
 */
class YtUploads extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'yt_uploads';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_queue');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['key', 'yt_link'], 'required'],
            [['key', 'yt_link'], 'string', 'max' => 255],
            [['key'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'key' => 'Key',
            'yt_link' => 'Yt Link',
        ];
    }
}
