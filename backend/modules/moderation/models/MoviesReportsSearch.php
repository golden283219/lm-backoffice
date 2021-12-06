<?php

namespace backend\modules\moderation\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\moderation\models\MoviesReports;

/**
 * MoviesReportsSearch represents the model behind the search form about `backend\modules\moderation\models\MoviesReports`.
 */
class MoviesReportsSearch extends MoviesReports
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_movie', 'sound_probm', 'connection_probm', 'label_probm', 'video_probm', 'subs_probm', 'year', 'id_user', 'notify_user', 'unseen', 'is_closed'], 'integer'],
            [['user_email', 'slug', 'title', 'message'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = MoviesReports::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'id_movie' => $this->id_movie,
            'sound_probm' => $this->sound_probm,
            'connection_probm' => $this->connection_probm,
            'label_probm' => $this->label_probm,
            'video_probm' => $this->video_probm,
            'subs_probm' => $this->subs_probm,
            'year' => $this->year,
            'id_user' => $this->id_user,
            'notify_user' => $this->notify_user,
            'unseen' => $this->unseen,
            'is_closed' => $this->is_closed,
        ]);

        $query->andFilterWhere(['like', 'user_email', $this->user_email])
            ->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'message', $this->message]);

        return $dataProvider;
    }
}
