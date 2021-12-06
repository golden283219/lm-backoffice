<?php

namespace backend\modules\system\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\CollectionHistory;

/**
 * CollectionHistorySearch represents the model behind the search form about `common\models\CollectionHistory`.
 */
class CollectionHistorySearch extends CollectionHistory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'collection_id', 'count_items'], 'integer'],
            [['started', 'finished'], 'safe'],
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
        $query = CollectionHistory::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'collection_id' => $this->collection_id,
            'started' => $this->started,
            'finished' => $this->finished,
            'count_items' => $this->count_items,
        ]);

        return $dataProvider;
    }
}
