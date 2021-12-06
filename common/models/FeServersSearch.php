<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\FeServers;

/**
 * FeServersSearch represents the model behind the search form about `common\models\FeServers`.
 */
class FeServersSearch extends FeServers
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'max_bw', 'is_enabled', 'is_hidden'], 'integer'],
            [['ip', 'server_name', 'status_check_url', 'created_at', 'updated_at', 'domain_mapped'], 'safe'],
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
        $query = FeServers::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'max_bw' => $this->max_bw,
            'is_enabled' => $this->is_enabled,
            'is_hidden' => $this->is_hidden,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'ip', $this->ip])
            ->andFilterWhere(['like', 'server_name', $this->server_name])
            ->andFilterWhere(['like', 'status_check_url', $this->status_check_url]);

        return $dataProvider;
    }
}
