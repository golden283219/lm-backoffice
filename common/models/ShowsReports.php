<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "shows_reports".
 *
 * @property int $id
 * @property int $id_show
 * @property int $sound_probm
 * @property int $connection_probm
 * @property int $label_probm
 * @property int $video_probm
 * @property int $subs_probm
 * @property string $slug
 * @property string $title
 * @property string $user_email
 * @property int $year
 * @property int $id_episode
 * @property int $episode
 * @property int $season
 * @property string $message
 * @property int $id_user
 * @property int $created_at
 * @property int $unseen
 * @property int $notify_user
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
class ShowsReports extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shows_reports';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_show'], 'required'],
            [['id_show', 'sound_probm', 'connection_probm', 'label_probm', 'video_probm', 'subs_probm', 'year', 'id_episode', 'episode', 'season', 'id_user', 'created_at', 'unseen', 'notify_user', 'is_closed', 'ip_addr'], 'integer'],
            [['message'], 'string'],
            [['slug', 'title', 'user_email'], 'string', 'max' => 200],
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
            'id_show' => 'Id Show',
            'sound_probm' => 'Sound Probm',
            'connection_probm' => 'Connection Probm',
            'label_probm' => 'Label Probm',
            'video_probm' => 'Video Probm',
            'subs_probm' => 'Subs Probm',
            'slug' => 'Slug',
            'title' => 'Title',
            'user_email' => 'User Email',
            'year' => 'Year',
            'id_episode' => 'Id Episode',
            'episode' => 'Episode',
            'season' => 'Season',
            'message' => 'Message',
            'id_user' => 'Id User',
            'created_at' => 'Created At',
            'unseen' => 'Unseen',
            'notify_user' => 'Notify User',
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
}