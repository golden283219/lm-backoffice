<?php

namespace backend\modules\system\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\CollectionData;

/**
 * CollectionDataSearch represents the model behind the search form about `common\models\CollectionData`.
 */
class CollectionDataSearch extends CollectionData
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['collection_data_id', 'collection_id', 'imdb_id', 'movie_id'], 'integer'],
            [['type'], 'safe'],
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
        $query = CollectionData::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params, '') && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'collection_data_id' => $this->collection_data_id,
            'collection_id' => $this->collection_id,
            'imdb_id' => $this->imdb_id,
            'movie_id' => $this->movie_id,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type]);

        return $dataProvider;
    }
}
