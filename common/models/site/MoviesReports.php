<?php

namespace common\models\site;

use Yii;

/**
 * This is the model class for table "movies_reports".
 *
 * @property int $id
 * @property int $id_movie
 * @property int $sound_probm
 * @property int $connection_probm
 * @property int $label_probm
 * @property int $video_probm
 * @property int $subs_probm
 * @property string $user_email
 * @property string $slug
 * @property string $title
 * @property int $year
 * @property string $message
 * @property int $created_at
 * @property int $id_user
 * @property int $notify_user
 * @property int $unseen
 * @property int $is_closed
 */
class MoviesReports extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'movies_reports';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_movie'], 'required'],
            [['id_movie', 'sound_probm', 'connection_probm', 'label_probm', 'video_probm', 'subs_probm', 'year', 'created_at', 'id_user', 'notify_user', 'unseen', 'is_closed'], 'integer'],
            [['message'], 'string'],
            [['user_email', 'slug', 'title'], 'string', 'max' => 200],
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
            'sound_probm' => 'Sound Probm',
            'connection_probm' => 'Connection Probm',
            'label_probm' => 'Label Probm',
            'video_probm' => 'Video Probm',
            'subs_probm' => 'Subs Probm',
            'user_email' => 'User Email',
            'slug' => 'Slug',
            'title' => 'Title',
            'year' => 'Year',
            'message' => 'Message',
            'created_at' => 'Created At',
            'id_user' => 'Id User',
            'notify_user' => 'Notify User',
            'unseen' => 'Unseen',
            'is_closed' => 'Is Closed',
        ];
    }
}
