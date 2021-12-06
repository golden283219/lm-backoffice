<?php

namespace common\models\queue;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\queue\Movies;

/**
 * MoviesSearch represents the model behind the search form about `\common\models\queue\Movies`.
 */
class MoviesSearch extends Movies
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'year', 'is_downloaded', 'priority', 'flag_quality', 'type'], 'integer'],
            [['title', 'imdb_id', 'url', 'source', 'bad_guids', 'bad_titles', 'worker_ip', 'updated_at', 'original_language', 'torrent_blob', 'rel_title'], 'safe'],
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
        $query = Movies::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'year' => $this->year,
            'is_downloaded' => $this->is_downloaded,
            'priority' => $this->priority,
            'flag_quality' => $this->flag_quality,
            'updated_at' => $this->updated_at,
            'type' => $this->type,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'imdb_id', $this->imdb_id])
            ->andFilterWhere(['like', 'url', $this->url])
            ->andFilterWhere(['like', 'source', $this->source])
            ->andFilterWhere(['like', 'bad_guids', $this->bad_guids])
            ->andFilterWhere(['like', 'bad_titles', $this->bad_titles])
            ->andFilterWhere(['like', 'worker_ip', $this->worker_ip])
            ->andFilterWhere(['like', 'original_language', $this->original_language])
            ->andFilterWhere(['like', 'torrent_blob', $this->torrent_blob])
            ->andFilterWhere(['like', 'rel_title', $this->rel_title]);

        return $dataProvider;
    }
}
