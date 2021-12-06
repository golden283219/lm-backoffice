<?php

namespace api\modules\v1\models\site;

use common\models\ShowsNotifications;
use api\modules\v1\models\queue\SendNotification;
use yii\base\BaseObject;

class ShowsEpisodes extends \common\models\site\ShowsEpisodes
{
    const EVENT_NEW_EPISODE = 'Adding new episode';

    private static function getConnection ()
    {
        $connection_credits = [];

        $dsn_string = env('DB_DSN');

        $connection = explode(':', $dsn_string);

        $dsn_parts = explode(';', $connection['1']);

        foreach ($dsn_parts as $dsn_value) {
            $dsn_key_value = explode('=', $dsn_value);
            $connection_credits[$dsn_key_value['0']] = $dsn_key_value['1'];
        }

        return new \mysqli($connection_credits['host'], env('DB_QUEUE_USERNAME'), env('DB_QUEUE_PASSWORD'), $connection_credits['dbname'], $connection_credits['port']);
    }

    public function init(){
        // Search here
		$this->on(ShowsEpisodes::EVENT_NEW_EPISODE, [$this, 'sendEmailsToSubscribers']);
    }

    public function getAudio ()
    {
        return $this->hasMany(ShowsEpisodesAudio::className(), ['id_episode' => 'id']);
    }

    public function getSubtitles ()
    {
        return $this->hasMany(ShowsEpisodesSubtitles::className(), ['id_episode' => 'id']);
    }

    public function getGcastCandidate()
    {
        $response = [];

        $connection = self::getConnection();

        if ($connection->connect_error) {
            die("Cannot connect to databse " . $connection->connect_error);
        }

        mysqli_set_charset($connection, 'utf8');

        $connection->multi_query("
            SET @update_id := 0;
            UPDATE shows_episodes SET is_chromecast_supported = 2, id = (SELECT @update_id := id)
            WHERE is_chromecast_supported = 0
            ORDER BY air_date DESC
            LIMIT 1;
            SELECT * FROM shows_episodes WHERE id = @update_id;
        ");

        do {
            if (0 !== $connection->errno) {
                echo "Multi query failed: (" . $connection->errno . ") " . $connection->error;
                break;
            }

            if (false !== ($res = $connection->store_result())) {
                $response = $res->fetch_all(MYSQLI_ASSOC);
                $res->free();
            }

            if (false === ($connection->more_results())) {
                break;
            }

            $connection->next_result();
        } while (true);

        if (count($response) > 0) {
            return $response[0];
        }

        return $response;
    }

    public function sendEmailsToSubscribers($event){
        $show_id = $this->id_shows;
        $subscribers = ShowsNotifications::find()
            ->select(['shows_notifications.user_id', 'prem_users.email'])
            ->leftJoin('prem_users', '`prem_users`.`id` = `shows_notifications`.`user_id`')
            ->where(['show_id' => $show_id])
            ->all();

        if($subscribers){
            foreach($subscribers as $subscriber){
                // Open queue job
                $id = \Yii::$app->redisNotificationsQueue->push(new SendNotification([
                     'email' => $subscriber->email,
                ]));
            }
        }
    }
}
