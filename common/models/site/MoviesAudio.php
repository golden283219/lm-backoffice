<?php

namespace common\models\site;

use Yii;

/**
 * This is the model class for table "movies_audio".
 *
 * @property int $id
 * @property int $id_movie
 * @property string $shard
 * @property string $storage_path
 * @property int $type
 * @property string $lang_iso_code
 */
class MoviesAudio extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'movies_audio';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_movie', 'shard', 'storage_path', 'type', 'lang_iso_code'], 'required'],
            [['id_movie', 'type'], 'integer'],
            [['shard'], 'string', 'max' => 100],
            [['storage_path'], 'string', 'max' => 255],
            [['lang_iso_code'], 'string', 'max' => 10],
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
            'shard' => 'Shard',
            'storage_path' => 'Storage Path',
            'type' => 'Type',
            'lang_iso_code' => 'Lang Iso Code',
        ];
    }
}
