<?php

namespace backend\modules\moderation\models;

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
 * @property string $country
 * @property int $ip_addr
 * @property string $ua
 * @property string $fe_server
 * @property string $iso
 * @property string $os
 * @property string $browser
 * @property string $src_quality
 */
class MoviesReports extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'movies_reports';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_movie'], 'required'],
            [['id_movie', 'sound_probm', 'connection_probm', 'label_probm', 'video_probm', 'subs_probm', 'year', 'created_at', 'id_user', 'notify_user', 'unseen', 'is_closed', 'ip_addr'], 'integer'],
            [['message'], 'string'],
            [['user_email', 'slug', 'title'], 'string', 'max' => 200],
            [['country'], 'string', 'max' => 120],
            [['ua', 'src_quality'], 'string', 'max' => 255],
            [['fe_server', 'iso'], 'string', 'max' => 12],
            [['os', 'browser'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
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
            'country' => 'Country',
            'ip_addr' => 'Ip Addr',
            'ua' => 'Ua',
            'fe_server' => 'Fe Server',
            'iso' => 'Iso',
            'os' => 'Os',
            'browser' => 'Browser',
            'src_quality' => 'Src Quality',
        ];
    }

    public function get_active_reports_grouped_by_movie($page = 1, $per_page = 20)
    {
        $offset = $per_page * ($page - 1);

        $query = "
            SELECT DISTINCT(mr.id_movie), m.title, m.poster, m.YEAR, m.slug, m.imdb_id FROM movies_reports AS mr
            LEFT JOIN movies AS m ON (m.id_movie = mr.id_movie)
            WHERE mr.is_closed = 0
            LIMIT $per_page OFFSET $offset
        ";
    }
}