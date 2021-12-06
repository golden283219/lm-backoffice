<?php
/**
 * Created by PhpStorm.
 * User: dev136
 * Date: 06.08.2019
 * Time: 14:00
 */

namespace backend\models\queue;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * ShowsMetaSearch represents the model behind the search form about `common\models\queue\ShowsMeta`.
 */
class ShowsMetaSearch extends ShowsMeta
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_meta', 'id_tvshow', 'season', 'episode', 'state', 'priority', 'type'], 'integer'],
            [['title', 'air_date', 'worker_ip', 'bad_guids', 'bad_titles', 'torrent_blob', 'rel_title'], 'safe'],
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
     *
     * @throws \Exception
     */
    public function search($params)
    {
        $query = ShowsMeta::find();

        $id_show = ArrayHelper::getValue($params, 'ShowsMetaSearch.id_tvshow');
        $show_with_torrents = ArrayHelper::getValue($params, 'ShowsMetaSearch.show_with_torrents');

        // state value from query params and remove it
        $state = ArrayHelper::getValue($params, 'ShowsMetaSearch.state');
        ArrayHelper::setValue($params, 'ShowsMetaSearch.state', '');

        if (!is_null($state) && $state !== '') {
            $state = explode(';', $state);
        } else {
            $state = [];
        }

        // doing this to hide all results until user enters id_tvshow
        if (empty($id_show)) {
            $query->filterWhere(['id_tvshow' => -1]);
        }

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'pagination' => false
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->orderBy(['season' => SORT_ASC, 'episode' => SORT_ASC]);
        if (!is_null($show_with_torrents) && !$show_with_torrents) {
            $query->andWhere(['OR', ['torrent_blob' => null], ['torrent_blob' => '']]);
        }

        $query->andFilterWhere([
            'id_meta' => $this->id_meta,
            'id_tvshow' => $this->id_tvshow,
            'season' => $this->season,
            'state' => $state,
            'episode' => $this->episode,
            'air_date' => $this->air_date,
            'priority' => $this->priority,
            'type' => $this->type,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'worker_ip', $this->worker_ip])
            ->andFilterWhere(['like', 'bad_guids', $this->bad_guids])
            ->andFilterWhere(['like', 'bad_titles', $this->bad_titles])
            ->andFilterWhere(['like', 'rel_title', $this->rel_title]);

        return $dataProvider;
    }
}
