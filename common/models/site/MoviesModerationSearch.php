<?php

namespace common\models\site;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\site\MoviesModeration;

/**
 * MoviesModerationSearch represents the model behind the search form about `common\models\site\MoviesModeration`.
 */
class MoviesModerationSearch extends MoviesModeration
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_movie', 'quality_approved', 'finalized_subs', 'have_all_subs', 'is_locked', 'locked_by', 'locked_at', 'active_reports_count', 'latest_reports_timestamp'], 'integer'],
            [['missing_languages'], 'safe'],
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
        $query = MoviesModeration::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'id_movie' => $this->id_movie,
            'quality_approved' => $this->quality_approved,
            'finalized_subs' => $this->finalized_subs,
            'have_all_subs' => $this->have_all_subs,
            'is_locked' => $this->is_locked,
            'locked_by' => $this->locked_by,
            'locked_at' => $this->locked_at,
            'active_reports_count' => $this->active_reports_count,
            'latest_reports_timestamp' => $this->latest_reports_timestamp,
        ]);

        $query->andFilterWhere(['like', 'missing_languages', $this->missing_languages]);

        return $dataProvider;
    }
}
