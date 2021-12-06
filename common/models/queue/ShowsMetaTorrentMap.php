<?php

namespace common\models\queue;

use Yii;

/**
 * This is the model class for table "shows_meta_torrent_map".
 *
 * @property int $id
 * @property int|null $id_meta
 * @property int|null $status
 * @property int|null $id_torrents_registry
 */
class ShowsMetaTorrentMap extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shows_meta_torrent_map';
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
            [['id_meta', 'status', 'id_torrents_registry'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_meta' => 'Id Meta',
            'status' => 'Status',
            'id_torrents_registry' => 'Id Torrents Registry',
        ];
    }
}
