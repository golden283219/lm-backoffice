<?php

namespace common\models\site;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\site\ShowsEpisodesReportsCache;

/**
 * ShowsEpisodesReportsCacheSearch represents the model behind the search form about `common\models\site\ShowsEpisodesReportsCache`.
 */
class ShowsEpisodesReportsCacheSearch extends ShowsEpisodesReportsCache
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'count', 'id_episode', 'assigned_user_id', 'is_closed'], 'integer'],
            [['last_reported_at'], 'safe'],
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
        $query = ShowsEpisodesReportsCache::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'count' => $this->count,
            'id_episode' => $this->id_episode,
            'last_reported_at' => $this->last_reported_at,
            'assigned_user_id' => $this->assigned_user_id,
            'is_closed' => $this->is_closed,
        ]);

        return $dataProvider;
    }
}
