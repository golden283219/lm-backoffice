<?php
/**
 * Created by PhpStorm.
 * User: dev136
 * Date: 06.08.2019
 * Time: 13:57
 */

namespace backend\models\queue;

class Shows extends \common\models\queue\Shows
{
    const STATUS_SUCCESS = 1;
    const STATUS_ERROR = 0;
    const STATUS_EXISTS = 2;

    /**
     * @param $term
     *
     * @return mixed
     *
     * @throws \yii\base\InvalidConfigException
     */
    public static function QueryTerm ($term)
    {
        $where_query = '';

        if ($term !== '') {
            $where_query = ' WHERE title LIKE \'%'.$term.'%\'';
        }

        $sql = "SELECT title, id_tvshow, first_air_date as year FROM shows $where_query LIMIT 20";

        $connection = \Yii::$app->get('db_queue');
        $command = $connection->createCommand($sql);
        $result = $command->queryAll();
        $connection->close();

        return $result;
    }

    /**
     * @param $id_show
     *
     * @return array
     */
    public static function getAllEpisodes($id_show)
    {
        $episodes = [];

        $episodes_heap = ShowsMeta::find()
            ->where(['id_tvshow' => $id_show])
            ->orderBy(['season' => SORT_ASC, 'episode' => SORT_ASC])
            ->asArray()
            ->all();

        foreach ($episodes_heap as $episode) {
            if (empty($episodes[$episode['season']])) {
                $episodes[$episode['season']] = [];
            }
            $episodes[$episode['season']][] = $episode;
        }

        return $episodes;
    }
}
