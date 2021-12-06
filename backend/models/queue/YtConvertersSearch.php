<?php

namespace backend\models\queue;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\queue\YtConverters;

/**
 * YtConvertersSearch represents the model behind the search form about `backend\models\queue\YtConverters`.
 */
class YtConvertersSearch extends YtConverters
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type'], 'integer'],
            [['ip', 'server_name', 'status_check_url'], 'safe'],
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
        $query = YtConverters::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
        ]);

        $query->andFilterWhere(['like', 'ip', $this->ip])
            ->andFilterWhere(['like', 'server_name', $this->server_name])
            ->andFilterWhere(['like', 'status_check_url', $this->status_check_url]);

        return $dataProvider;
    }
}
