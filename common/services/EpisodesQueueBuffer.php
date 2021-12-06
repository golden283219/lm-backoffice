<?php


namespace common\services;

use backend\models\queue\ShowsMeta;
use Redis;

class EpisodesQueueBuffer
{
    /**
     * @var Redis
     */
    private $redis;

    const REDIS_KEY_NAME = 'episodes.jobs.queue';

    public function __construct()
    {
        if (is_null($this->redis)) {
            $this->redis_connect();
        }
    }

    /**
     * Get Job From Queue
     *
     * @param $statusCode
     *
     * @return array|\yii\db\ActiveRecord|null
     */
    public function getJob($statusCode)
    {
        $job_id = $this->redis->rPop($this::REDIS_KEY_NAME . $statusCode);

        if (!$job_id) {
            return [];
        }

        return ShowsMeta::QueryCandidateById($job_id);
    }

    /**
     * Fill jobs queue to specified limit
     *
     * @param int $limit
     */
    public function fillJobsQueue($limit = 10)
    {
        $status_codes = explode(',', env('EPISODES_DOWNLOAD_QUEUE_CODES', '0,4'));

        foreach ($status_codes as $status_code) {
            $len = $this->redis->lLen($this::REDIS_KEY_NAME . $status_code);
            $len_diff = $limit - $len;
            if ($len_diff > 0) {
                $this->fill_jobs($len_diff, $status_code);
            }
        }
    }

    private function fill_jobs ($limit, $status_code)
    {
        $candidates = ShowsMeta::QueryCandidate($limit, $status_code);

        if (!is_array($candidates)) {
            return false;
        }

        foreach ($candidates as $candidate) {
            $this->redis->lPush($this::REDIS_KEY_NAME . $status_code, $candidate);
        }
    }

    private function redis_connect()
    {
        $this->redis = new Redis();
        $this->redis->connect(env('REDIS_HOST'), env('REDIS_PORT'), 1.5);
    }

}
