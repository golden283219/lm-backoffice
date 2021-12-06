<?php

namespace backend\models\site;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * MoviesFeaturedSearch represents the model behind the search form about `backend\modules\moderation\models\Movies`.
 */
class MoviesFeaturedSearch extends Movies
{
    // add the public attributes that will be used to store the data to be search
    public $position;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_movie', 'is_active', 'year', 'duration', 'budget', 'views', 'tmdb_prefix', 'has_metadata', 'has_subtitles', 'rel_size_bytes', 'has_hash', 'priority', 'flag_quality', 'transfer_status', 'position'], 'integer'],
            [['slug', 'shard_url', 'title', 'description', 'country', 'backdrop', 'homepage', 'imdb_id','tagline', 'poster', 'date_added', 'rel_title', 'rel_os_hash', 'youtube', 'release_date', 'storage_slug', 'cast', 'genres', 'imdb_id', 'position'], 'safe'],
            [['imdb_rating'], 'number'],
        ];
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
        $query = Movies::find()->innerJoinWith('moviesFeatured', true);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['position'] = [
            'asc' => ['movies_featured.position' => SORT_ASC],
            'desc' => ['movies_featured.position' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        /**
         * Search For IMDB_ID or for Full Title
         */
        if (!empty($params['MoviesFeaturedSearch']['title']) && !empty($imdb_id = extract_imdb_id($params['MoviesFeaturedSearch']['title']))) {
            $this->imdb_id = $imdb_id;
            $query->andFilterWhere(['imdb_id' => sanitize_imdb_id($imdb_id)]);
        } else {
            $query->andFilterWhere(['like', 'title', $this->title]);
        }

        if (!empty($params['MoviesFeaturedSearch']['position'])){
            $query->andFilterWhere(['position' => $this->position]);
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
            'has_hash' => $this->has_hash,
            'priority' => $this->priority,
            'transfer_status' => $this->transfer_status,
        ]);

        if ($this->flag_quality !== '') {
            if ($this->flag_quality < 7) {
                $query->andFilterWhere(['<', 'flag_quality', '7']);
            } else if($this->flag_quality == 7){
                $query->andFilterWhere(['=', 'flag_quality', '7']);
            } else if($this->flag_quality == 8){
                $query->andFilterWhere(['=','flag_quality', '8']);
            }
        }

        $query->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'shard_url', $this->shard_url])
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
            ->andFilterWhere(['like', 'genres', $this->genres]);

        return $dataProvider;
    }
}
