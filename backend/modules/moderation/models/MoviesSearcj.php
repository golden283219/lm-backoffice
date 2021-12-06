<?php

namespace backend\modules\moderation\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\moderation\models\Movies;

/**
 * MoviesSearcj represents the model behind the search form about `backend\modules\moderation\models\Movies`.
 */
class MoviesSearcj extends Movies
{

    public $quality_approved;
    public $finalized_subs;
    public $have_all_subs;
    public $is_locked;
    public $locked_at;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_movie', 'is_active', 'year', 'duration', 'budget', 'views', 'tmdb_prefix', 'has_metadata', 'has_subtitles', 'rel_size_bytes', 'has_hash', 'priority', 'flag_quality', 'transfer_status', 'quality_approved', 'finalized_subs', 'have_all_subs', 'is_locked', 'locked_at'], 'integer'],
            [['slug', 'shard_url', 'title', 'description', 'country', 'backdrop', 'homepage', 'tagline', 'poster', 'date_added', 'rel_title', 'rel_os_hash', 'youtube', 'release_date', 'storage_slug', 'cast', 'genres', 'imdb_id'], 'safe'],
            [['imdb_rating'], 'number'],
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
        $query = Movies::find()->innerJoinWith('moviesModeration', true);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id_movie' => $this->id_movie,
            'is_active' => $this->is_active,
            'year' => $this->year,
            'duration' => $this->duration,
            'imdb_rating' => $this->imdb_rating,
            'budget' => $this->budget,
            'views' => $this->views,
            'date_added' => $this->date_added,
            'tmdb_prefix' => $this->tmdb_prefix,
            'has_metadata' => $this->has_metadata,
            'has_subtitles' => $this->has_subtitles,
            'rel_size_bytes' => $this->rel_size_bytes,
            'release_date' => $this->release_date,
            'quality_approved' => $this->quality_approved,
            'finalized_subs' => $this->finalized_subs,
            'have_all_subs' => $this->have_all_subs,
            'is_locked' => $this->is_locked,
            'locked_at' => $this->locked_at,
            'has_hash' => $this->has_hash,
            'priority' => $this->priority,
            'flag_quality' => $this->flag_quality,
            'transfer_status' => $this->transfer_status,
        ]);

        $query->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'shard_url', $this->shard_url])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'country', $this->country])
            ->andFilterWhere(['like', 'backdrop', $this->backdrop])
            ->andFilterWhere(['like', 'homepage', $this->homepage])
            ->andFilterWhere(['like', 'tagline', $this->tagline])
            ->andFilterWhere(['like', 'poster', $this->poster])
            ->andFilterWhere(['like', 'rel_title', $this->rel_title])
            ->andFilterWhere(['like', 'rel_os_hash', $this->rel_os_hash])
            ->andFilterWhere(['like', 'youtube', $this->youtube])
            ->andFilterWhere(['like', 'storage_slug', $this->storage_slug])
            ->andFilterWhere(['like', 'cast', $this->cast])
            ->andFilterWhere(['like', 'genres', $this->genres])
            ->andFilterWhere(['like', 'imdb_id', $this->imdb_id]);

        return $dataProvider;
    }
}
