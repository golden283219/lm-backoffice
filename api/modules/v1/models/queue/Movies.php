<?php

namespace api\modules\v1\models\queue;

use \common\models\queue\Movies as MoviesQueue;

class Movies extends MoviesQueue
{

    const TYPE_TORRENT = 0;
    const TYPE_MAGNET = 1;

    private static function getConnection ()
    {
        $connection_credits = [];

        $dsn_string = env('DB_QUEUE_DSN');

        $connection = explode(':', $dsn_string);

        $dsn_parts = explode(';', $connection['1']);
        
        foreach ($dsn_parts as $dsn_value) {
            $dsn_key_value = explode('=', $dsn_value);
            $connection_credits[$dsn_key_value['0']] = $dsn_key_value['1'];
        }

        return new \mysqli($connection_credits['host'], env('DB_QUEUE_USERNAME'), env('DB_QUEUE_PASSWORD'), $connection_credits['dbname'], $connection_credits['port']);
    }

    public static function get_candidate ($status)
    {

        $connection = self::getConnection();

        if ($connection->connect_error) {
            die("Cannot connect to databse " . $connection->connect_error);
        }

        mysqli_set_charset($connection, 'utf8');

        $response = null;

        $remote_addr = $_SERVER['REMOTE_ADDR']; 

        $connection->multi_query("
            SET @update_id := 0;
            UPDATE movies SET is_downloaded = 3, worker_ip = '$remote_addr', id = (SELECT @update_id := id)
            WHERE is_downloaded = $status
            AND imdb_id <> '-1'
            AND imdb_id <> ''
            ORDER BY priority DESC, year DESC
            LIMIT 1;
            SELECT * FROM movies WHERE id = @update_id;
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

    public static function get_en_candidate ($status)
    {
        $connection = self::getConnection();

        if ($connection->connect_error) {
            die("Cannot connect to databse " . $connection->connect_error);
        }

        mysqli_set_charset($connection, 'utf8');

        $response = null;

        $remote_addr = $_SERVER['REMOTE_ADDR']; 

        $connection->multi_query("
            SET @update_id := 0;
            UPDATE movies SET is_downloaded = 3, worker_ip = '$remote_addr', id = (SELECT @update_id := id)
            WHERE is_downloaded = $status
            AND imdb_id <> '-1'
            AND imdb_id <> ''
            AND original_language = 'en'
            ORDER BY priority DESC, year DESC
            LIMIT 1;
            SELECT * FROM movies WHERE id = @update_id;
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

    public function init()
    {
        parent::init();

        $this->on(self::EVENT_BEFORE_UPDATE, 'handle_update_movie_moderation_history');
    }

    public function detachEvents()
    {
        $this->off(self::EVENT_BEFORE_UPDATE, 'handle_update_movie_moderation_history');
    }
}
