<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ShowsEpisodes;

/**
 * ShowsEpisodesSearch represents the model behind the search form about `common\models\ShowsEpisodes`.
 */
class ShowsEpisodesSearch extends ShowsEpisodes
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_shows', 'is_active', 'episode', 'season', 'subtitles_state', 'has_metadata', 'flag_quality', 'is_locked', 'quality_approved', 'finalized_subs', 'have_all_subs', 'missing_languages', 'subs_count', 'locked_by', 'locked_at'], 'integer'],
            [['title', 'description', 'still_path', 'shard', 'storage', 'air_date', 'rel_title'], 'safe'],
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
        $query = ShowsEpisodes::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'id_shows' => $this->id_shows,
            'is_active' => $this->is_active,
            'episode' => $this->episode,
            'season' => $this->season,
            'subtitles_state' => $this->subtitles_state,
            'air_date' => $this->air_date,
            'has_metadata' => $this->has_metadata,
            'flag_quality' => $this->flag_quality,
            'is_locked' => $this->is_locked,
            'quality_approved' => $this->quality_approved,
            'finalized_subs' => $this->finalized_subs,
            'have_all_subs' => $this->have_all_subs,
            'missing_languages' => $this->missing_languages,
            'subs_count' => $this->subs_count,
            'locked_by' => $this->locked_by,
            'locked_at' => $this->locked_at,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'still_path', $this->still_path])
            ->andFilterWhere(['like', 'shard', $this->shard])
            ->andFilterWhere(['like', 'storage', $this->storage])
            ->andFilterWhere(['like', 'rel_title', $this->rel_title]);

        return $dataProvider;
    }
}
