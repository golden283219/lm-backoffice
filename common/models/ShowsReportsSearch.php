<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ShowsReports;

/**
 * ShowsReportsSearch represents the model behind the search form about `common\models\ShowsReports`.
 */
class ShowsReportsSearch extends ShowsReports
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_show', 'sound_probm', 'connection_probm', 'label_probm', 'video_probm', 'subs_probm', 'year', 'id_episode', 'episode', 'season', 'id_user', 'created_at', 'unseen', 'notify_user', 'is_closed'], 'integer'],
            [['slug', 'title', 'user_email', 'message'], 'safe'],
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
        $query = ShowsReports::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'id_show' => $this->id_show,
            'sound_probm' => $this->sound_probm,
            'connection_probm' => $this->connection_probm,
            'label_probm' => $this->label_probm,
            'video_probm' => $this->video_probm,
            'subs_probm' => $this->subs_probm,
            'year' => $this->year,
            'id_episode' => $this->id_episode,
            'episode' => $this->episode,
            'season' => $this->season,
            'id_user' => $this->id_user,
            'created_at' => $this->created_at,
            'unseen' => $this->unseen,
            'notify_user' => $this->notify_user,
            'is_closed' => $this->is_closed,
        ]);

        $query->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'user_email', $this->user_email])
            ->andFilterWhere(['like', 'message', $this->message]);

        return $dataProvider;
    }
}
