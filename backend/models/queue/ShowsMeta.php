<?php
/**
 * Created by PhpStorm.
 * User: dev136
 * Date: 27.03.2020
 * Time: 23:45
 */

namespace backend\models\queue;

use Redis;
use Yii;
use yii\db\Query;
use yii\db\QueryBuilder;
use yii\helpers\ArrayHelper;

class ShowsMeta extends \common\models\queue\ShowsMeta
{

    public static $ignore_history_init = true;

    const TYPE_TORRENT = 0;
    const TYPE_MAGNET = 1;
    const TYPE_MAPPED_MAGNET = 2;

    public static $stateTypes = [
        0 => 'WAITING',
        1 => 'ON SITE',
        3 => 'BEING CONVERTED',
        4 => 'WAITING(torrent)',
        5 => 'NO CANDIDATE',
    ];

    public function init()
    {
        parent::init();

        $this->on(self::EVENT_AFTER_INSERT, 'handle_init_episode_moderation_history');
        $this->on(self::EVENT_BEFORE_UPDATE, 'handle_init_episode_moderation_history');

        $this->on(self::EVENT_BEFORE_INSERT, ['\backend\models\queue\ShowsMetaTorrentMap', 'handle_multi_torrent_logs'], $this);
        $this->on(self::EVENT_BEFORE_UPDATE, ['\backend\models\queue\ShowsMetaTorrentMap', 'handle_multi_torrent_logs'], $this);

        $this->on(self::EVENT_BEFORE_UPDATE, [self::class, 'handle_torrent_map_status'], $this);
    }

    public function detachEvents()
    {
        $this->off(self::EVENT_AFTER_INSERT, 'handle_init_episode_moderation_history');
        $this->off(self::EVENT_BEFORE_UPDATE, 'handle_init_episode_moderation_history');
    }

    public function getShow ()
    {
        return $this->hasOne(Shows::className(), ['id_tvshow' => 'id_tvshow']);
    }

    public static function handle_torrent_map_status($event)
    {
        $shows_meta = $event->data;

        // new values
        $dirty_attr = $shows_meta->getDirtyAttributes();
        // old values
        $old_attr = $shows_meta->getOldAttributes();

        if (!empty($dirty_attr['map'])) {
            return true;
        }

        if (!empty($dirty_attr['torrent_blob'])) {
            $shows_meta->map = null;
        }

        $dirty_state = ArrayHelper::getValue($dirty_attr, 'state');
        // 1, 5, 4
        if ($dirty_state == 1 || $dirty_state == 5) {
            $shows_meta->map = null;
        }

        return true;
    }

    public static function QueryCandidateById ($id)
    {
        $connection = self::getDb();

        $ip = $_SERVER['REMOTE_ADDR'];

        $connection->createCommand("
            UPDATE shows_meta
            SET state = 3, worker_ip = '$ip'
            WHERE id_meta = $id
        ")->execute();

        $query = self::find()
            ->select([
                'id_meta',
                'shows_meta.id_tvshow',
                'season',
                'episode',
                'shows.title',
                'air_date',
                'worker_ip',
                'bad_guids',
                'bad_titles',
                'state',
                'priority',
                'torrent_blob',
                'type',
                'rel_title',
                'history_guid',
                'flag_quality',
                'link',
                'size',
                'first_air_date',
                'imdb_id',
                'tmdb_id',
                'tvmaze_id',
                'total_episodes',
                'total_seasons',
                'episode_duration',
                'in_production',
                'status',
                'date_added',
                'map',
                'data',
                'original_language',
                'tvdb_id',
                'tvmaze_updated_timestamp'
            ])
            ->joinWith('show', false)
            ->where(['id_meta' => $id]);

        return $query->asArray()->one();
    }

    /**
     * Query Candidates
     *
     * @param $limit
     * @param $status_code
     *
     * @return array
     */
    public static function QueryCandidate($limit, $status_code)
    {
        // select job from redis
        $jobs = self::query_jobs($limit, $status_code);
        $jobs_ids = array_map(function ($item) {
            return $item['id_meta'];
        }, $jobs);

        ShowsMeta::updateAll(['state' => '3'], ['id_meta' => $jobs_ids]);

        return $jobs_ids;
    }

    /**
     * Cancel all assigned jobs
     * to $worker_ip, except $id_meta
     *
     * @param $id_meta
     * @param $worker_ip
     */
    private static function cancel_all_assigned_jobs_except($id_meta, $worker_ip)
    {
        self::updateAll(['worker_ip' => null, 'state' => 4],"id_meta <> $id_meta AND worker_ip = '$worker_ip' AND state = 3");
    }

    private static function get_all_worker_jobs($worker_ip)
    {
        $query = self::find()
            ->select([
                'id_meta',
                'shows_meta.id_tvshow',
                'season',
                'episode',
                'shows.title',
                'air_date',
                'worker_ip',
                'bad_guids',
                'bad_titles',
                'state',
                'priority',
                'torrent_blob',
                'type',
                'rel_title',
                'history_guid',
                'flag_quality',
                'link',
                'size',
                'first_air_date',
                'imdb_id',
                'tmdb_id',
                'tvmaze_id',
                'total_episodes',
                'total_seasons',
                'episode_duration',
                'in_production',
                'status',
                'date_added',
                'data',
                'original_language',
                'tvdb_id',
                'tvmaze_updated_timestamp'
            ])
            ->joinWith('show', false)
            ->where(['worker_ip' => $worker_ip])
            ->orderBy(['updated_at' => SORT_DESC]);

        return $query->asArray()->all();
    }

    /**
     * @param $limit
     * @param $status_code
     *
     * @return array
     */
    private static function query_jobs($limit, $status_code)
    {
        $query = new Query;
        $query->select([
            'id_meta'
        ]);
        $query->from('shows_meta');
        $query->where([
            'state'   => $status_code,
        ]);
        $query->andWhere(['<=', 'air_date', date("Y-m-d")]);
        $query->orderBy([
            'priority' => SORT_DESC,
            'air_date' => SORT_DESC
        ]);
        $query->limit($limit);
        return $query->all(self::getDb());
    }
}
