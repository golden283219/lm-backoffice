<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "cast_imdb".
 *
 * @property int $id
 * @property string|null $imdb_actor_id
 * @property string|null $birth_name
 * @property string|null $full_name
 * @property string|null $nick_name
 * @property string|null $birth_place
 * @property string|null $birth_date
 * @property string|null $photo
 * @property string|null $bio
 * @property string|null $slug
 * @property int|null $knows_for_job
 * @property string|null $known_for_department
 * @property float|null $popularity
 * @property string|null $gender
 * @property string|null $also_known_as
 * @property string|null $facebook
 * @property string|null $instagram
 * @property string|null $twitter
 * @property string|null $homepage
 * @property string|null $deathday
 * @property int|null $known_credits
 *
 * @property KnownForMovies[] $knownForMovies
 * @property KnownForShows[] $knownForShows
 */
class CastImdb extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cast_imdb';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bio'], 'string'],
            [['knows_for_job', 'known_credits'], 'integer'],
            [['popularity'], 'number'],
            [['deathday'], 'safe'],
            [['imdb_actor_id'], 'string', 'max' => 12],
            [['birth_name', 'full_name', 'nick_name', 'birth_place', 'slug', 'known_for_department', 'facebook', 'instagram', 'twitter', 'homepage'], 'string', 'max' => 255],
            [['also_known_as'], 'string', 'max' => 65500],
            [['birth_date'], 'string', 'max' => 64],
            [['photo'], 'string', 'max' => 60],
            [['gender'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'imdb_actor_id' => 'Imdb Actor ID',
            'birth_name' => 'Birth Name',
            'full_name' => 'Full Name',
            'nick_name' => 'Nick Name',
            'birth_place' => 'Birth Place',
            'birth_date' => 'Birth Date',
            'photo' => 'Photo',
            'bio' => 'Bio',
            'slug' => 'Slug',
            'knows_for_job' => 'Knows For Job',
            'known_for_department' => 'Known For Department',
            'popularity' => 'Popularity',
            'gender' => 'Gender',
            'also_known_as' => 'Also Known As',
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'twitter' => 'Twitter',
            'homepage' => 'Homepage',
            'deathday' => 'Deathday',
            'known_credits' => 'Known Credits',
        ];
    }

    /**
     * Gets query for [[KnownForMovies]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKnownForMovies()
    {
        return $this->hasMany(KnownForMovies::className(), ['id_cast' => 'id']);
    }

    /**
     * Gets query for [[KnownForShows]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKnownForShows()
    {
        return $this->hasMany(KnownForShows::className(), ['id_cast' => 'id']);
    }
}
