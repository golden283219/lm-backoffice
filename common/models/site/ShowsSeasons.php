<?php

/**
 * @property $season
 */

namespace common\models\site;

use Yii;

class ShowsSeasons extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'shows_seasons';
    }

    public function rules()
    {
        return [
            [['id_show', 'season'], 'required'],
            [['id_show', 'season'], 'integer'],
            [['description', 'title', 'poster'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id_seasons' => 'ID Season',
            'id_show' => 'ID Show',
            'first_air_date' => 'First Air Date',
            'description' => 'Description',
            'title' => 'Title',
            'season' => 'Season',
            'poster' => 'Poster',
        ];
    }
}
