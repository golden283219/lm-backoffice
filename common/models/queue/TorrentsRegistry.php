<?php

namespace common\models\queue;

use Yii;

/**
 * This is the model class for table "torrents_registry".
 *
 * @property int $id
 * @property int|null $type
 * @property string|null $torrent_contents
 * @property int|null $status
 * @property string|null $id_torrent
 */
class TorrentsRegistry extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'torrents_registry';
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
            [['type', 'status'], 'integer'],
            [['torrent_contents'], 'string'],
            [['id_torrent'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'torrent_contents' => 'Torrent Contents',
            'status' => 'Status',
            'id_torrent' => 'Id Torrent',
        ];
    }
}
