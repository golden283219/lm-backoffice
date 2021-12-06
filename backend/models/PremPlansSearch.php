<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\PremPlans;

/**
 * PremPlansSearch represents the model behind the search form about `backend\models\PremPlans`.
 */
class PremPlansSearch extends PremPlans
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'extra_time', 'is_default', 'is_active', 'month_count'], 'integer'],
            [['price_usd', 'discount'], 'number'],
            [['title', 'description', 'code', 'affiliate_tariff_maping'], 'safe'],
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
        $query = PremPlans::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'price_usd' => $this->price_usd,
            'discount' => $this->discount,
            'extra_time' => $this->extra_time,
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
            'month_count' => $this->month_count,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'affiliate_tariff_maping', $this->affiliate_tariff_maping]);

        return $dataProvider;
    }
}
