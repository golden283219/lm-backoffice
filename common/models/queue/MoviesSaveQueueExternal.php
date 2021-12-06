<?php

namespace common\models\queue;

use Yii;

/**
 * This is the model class for table "movies_save_queue_external".
 *
 * @property int $id
 * @property int $id_movie
 * @property string $worker_ip
 * @property array $files
 * @property string $rel_title
 * @property string $slug
 * @property string $storage_slug
 * @property int $status
 * @property int $is_dd
 * @property int $flag_quality
 * @property int $size_bytes
 * @property string $os_hash
 * @property int $id_process
 * @property string $lang_iso_code
 * @property string $created_at
 * @property string $updated_at
 * @property array $subs
 */
class MoviesSaveQueueExternal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'movies_save_queue_external';
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
            [['id_movie', 'worker_ip', 'files', 'rel_title', 'slug', 'storage_slug', 'id_process', 'lang_iso_code'], 'required'],
            [['id_movie', 'status', 'is_dd', 'flag_quality', 'size_bytes', 'id_process'], 'integer'],
            [['files', 'created_at', 'updated_at', 'subs'], 'safe'],
            [['worker_ip', 'rel_title', 'slug', 'storage_slug', 'os_hash', 'lang_iso_code'], 'string', 'max' => 255],
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
            'worker_ip' => 'Worker Ip',
            'files' => 'Files',
            'rel_title' => 'Rel Title',
            'slug' => 'Slug',
            'storage_slug' => 'Storage Slug',
            'status' => 'Status',
            'is_dd' => 'Is Dd',
            'flag_quality' => 'Flag Quality',
            'size_bytes' => 'Size Bytes',
            'os_hash' => 'Os Hash',
            'id_process' => 'Id Process',
            'lang_iso_code' => 'Lang Iso Code',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'subs' => 'Subs',
        ];
    }
}
