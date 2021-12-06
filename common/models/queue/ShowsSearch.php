<?php

namespace common\models\queue;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\queue\Shows;

/**
 * ShowsSearch represents the model behind the search form about `common\models\queue\Shows`.
 */
class ShowsSearch extends Shows
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_tvshow', 'total_episodes', 'total_seasons', 'episode_duration', 'in_production', 'status'], 'integer'],
            [['title', 'first_air_date', 'imdb_id', 'tmdb_id', 'tvmaze_id', 'date_added', 'data', 'original_language'], 'safe'],
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
        $query = Shows::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id_tvshow' => $this->id_tvshow,
            'first_air_date' => $this->first_air_date,
            'total_episodes' => $this->total_episodes,
            'total_seasons' => $this->total_seasons,
            'episode_duration' => $this->episode_duration,
            'in_production' => $this->in_production,
            'status' => $this->status,
            'date_added' => $this->date_added,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'imdb_id', $this->imdb_id])
            ->andFilterWhere(['like', 'tmdb_id', $this->tmdb_id])
            ->andFilterWhere(['like', 'tvmaze_id', $this->tvmaze_id])
            ->andFilterWhere(['like', 'data', $this->data])
            ->andFilterWhere(['like', 'original_language', $this->original_language]);

        return $dataProvider;
    }
}
