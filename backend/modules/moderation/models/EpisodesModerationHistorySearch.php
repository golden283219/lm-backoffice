<?php

namespace backend\modules\moderation\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\moderation\models\EpisodesModerationHistory;

/**
 * EpisodesModerationHistorySearch represents the model behind the search form about `backend\modules\moderation\models\EpisodesModerationHistory`.
 */
class EpisodesModerationHistorySearch extends EpisodesModerationHistory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_meta', 'air_date', 'priority', 'id_user', 'status', 'type', 'is_deleted'], 'integer'],
            [['title', 'imdb_id', 'tvmaze_id', 'original_language', 'data', 'guid', 'worker_ip', 'created_at', 'updated_at'], 'safe'],
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
    public function search($params, $disablePagination = false)
    {
        $query = EpisodesModerationHistory::find();
        $activeQueryParams = [
            'query' => $query,
        ];
        if ($disablePagination) {
            $activeQueryParams['pagination'] = false;
        }
        $dataProvider = new ActiveDataProvider($activeQueryParams);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'id_meta' => $this->id_meta,
            'air_date' => $this->air_date,
            'priority' => $this->priority,
            'id_user' => $this->id_user,
            'status' => $this->status,
            'type' => $this->type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_deleted' => $this->is_deleted,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'imdb_id', $this->imdb_id])
            ->andFilterWhere(['like', 'tvmaze_id', $this->tvmaze_id])
            ->andFilterWhere(['like', 'original_language', $this->original_language])
            ->andFilterWhere(['like', 'data', $this->data])
            ->andFilterWhere(['like', 'guid', $this->guid])
            ->andFilterWhere(['like', 'worker_ip', $this->worker_ip]);

        return $dataProvider;
    }
}
