<?php

namespace backend\modules\moderation\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\moderation\models\ModerationDraft;

/**
 * DraftSearch represents the model behind the search form about `backend\modules\moderation\models\ModerationDraft`.
 */
class DraftSearch extends ModerationDraft
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_media', 'created_by', 'executed_by', 'status', 'created_at', 'category'], 'integer'],
            [['title'], 'safe'],
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
        $query = ModerationDraft::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'id_media' => $this->id_media,
            'created_by' => $this->created_by,
            'executed_by' => $this->executed_by,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'category', $this->category]);

        return $dataProvider;
    }
}
