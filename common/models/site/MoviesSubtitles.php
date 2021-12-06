<?php

namespace common\models\site;

use Yii;

/**
 * This is the model class for table "movies_subtitles".
 *
 * @property int $id
 * @property int $id_movie
 * @property string $url
 * @property string $language
 * @property int $is_approved
 * @property int $is_moderated
 * @property string|null $shard
 * @property string $hash
 * @property string $release_title
 * @property string|null $source
 * @property int|null $source_id
 * @property float|null $score
 * @property string|null $format
 */
class MoviesSubtitles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'movies_subtitles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_movie', 'url', 'language'], 'required'],
            [['id_movie', 'is_approved', 'is_moderated', 'source_id'], 'integer'],
            [['score'], 'number'],
            [['url'], 'string', 'max' => 255],
            [['language'], 'string', 'max' => 120],
            [['shard'], 'string', 'max' => 180],
            [['hash'], 'string', 'max' => 32],
            [['release_title'], 'string', 'max' => 100],
            [['source', 'format'], 'string', 'max' => 50],
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
            'url' => 'Url',
            'language' => 'Language',
            'is_approved' => 'Is Approved',
            'is_moderated' => 'Is Moderated',
            'shard' => 'Shard',
            'hash' => 'Hash',
            'release_title' => 'Release Title',
            'source' => 'Source',
            'source_id' => 'Source ID',
            'score' => 'Score',
            'format' => 'Format',
        ];
    }
}
