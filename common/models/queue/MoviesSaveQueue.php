<?php

namespace common\models\queue;

use Yii;

/**
 * This is the model class for table "movies_save_queue".
 *
 * @property int $id
 * @property int $id_movie
 * @property string $slug
 * @property string $storage_slug
 * @property string $rel_title
 * @property string $worker_ip
 * @property int $is1080p
 * @property int $is720p
 * @property int $is480p
 * @property int $is360p
 * @property int $is_dd
 * @property int $size_bytes
 * @property string $os_hash
 * @property int $flag_quality
 * @property int $status
 * @property int $id_process
 * @property string $lang_iso_code
 */
class MoviesSaveQueue extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'movies_save_queue';
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
            [['id_movie', 'slug', 'storage_slug', 'is1080p', 'is720p', 'is480p', 'is360p', 'size_bytes', 'os_hash'], 'required'],
            [['id_movie', 'is1080p', 'is720p', 'is480p', 'is360p', 'is_dd', 'size_bytes', 'flag_quality', 'status', 'id_process'], 'integer'],
            [['slug', 'storage_slug', 'os_hash'], 'string', 'max' => 255],
            [['rel_title'], 'string', 'max' => 500],
            [['worker_ip'], 'string', 'max' => 100],
            [['lang_iso_code'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_movie' => 'Id Movie',
            'slug' => 'Slug',
            'storage_slug' => 'Storage Slug',
            'rel_title' => 'Rel Title',
            'worker_ip' => 'Worker Ip',
            'is1080p' => 'Is1080p',
            'is720p' => 'Is720p',
            'is480p' => 'Is480p',
            'is360p' => 'Is360p',
            'is_dd' => 'Is Dd',
            'size_bytes' => 'Size Bytes',
            'os_hash' => 'Os Hash',
            'flag_quality' => 'Flag Quality',
            'status' => 'Status',
            'id_process' => 'Id Process',
            'lang_iso_code' => 'Lang Iso Code',
        ];
    }
}
