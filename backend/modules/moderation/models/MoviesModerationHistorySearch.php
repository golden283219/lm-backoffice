<?php

namespace backend\modules\moderation\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * MoviesModerationHistorySearch represents the model behind the search form about `backend\modules\moderation\models\MoviesModerationHistory`.
 */
class MoviesModerationHistorySearch extends MoviesModerationHistory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_movie', 'year', 'priority', 'id_user', 'status', 'type', 'is_deleted'], 'integer'],
            [['title', 'imdb_id', 'original_language', 'data', 'guid', 'worker_ip', 'created_at', 'updated_at'], 'safe'],
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
        $query = MoviesModerationHistory::find();

        $query->where(['is_deleted' => 0]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'id_movie' => $this->id_movie,
            'year' => $this->year,
            'priority' => $this->priority,
            'id_user' => $this->id_user,
            'status' => $this->status,
            'type' => $this->type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_deleted' => $this->is_deleted
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'imdb_id', $this->imdb_id])
            ->andFilterWhere(['like', 'original_language', $this->original_language])
            ->andFilterWhere(['like', 'data', $this->data])
            ->andFilterWhere(['like', 'guid', $this->guid])
            ->andFilterWhere(['like', 'worker_ip', $this->worker_ip]);

        return $dataProvider;
    }
}
