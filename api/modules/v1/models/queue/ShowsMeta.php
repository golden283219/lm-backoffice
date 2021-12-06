<?php
/**
 * Created by PhpStorm.
 * User: dev136
 * Date: 06.08.2019
 * Time: 13:50
 */

namespace api\modules\v1\models\queue;

class ShowsMeta extends \common\models\queue\ShowsMeta
{
    public function getShow ()
    {
        return $this->hasOne(Shows::className(), ['id_tvshow' => 'id_tvshow']);
    }

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

    public static function QueryCandidateById ($id)
    {
        $connection = \Yii::$app->get('db_queue');

        $ip = $_SERVER['REMOTE_ADDR'];

        $connection->createCommand("
            UPDATE shows_meta
            SET state = 3, worker_ip = '$ip'
            WHERE id_meta = $id
        ")->execute();

        $response = $connection->createCommand("
            SELECT * FROM shows_meta
            LEFT JOIN shows as s ON (s.id_tvshow = shows_meta.id_tvshow)
            WHERE id_meta = $id
        ")->queryOne();

        return $response;
    }

    public static function QueryCandidateIgnorePriority ($status)
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
            UPDATE shows_meta SET state = 3, worker_ip = '$remote_addr', id_meta = (SELECT @update_id := id_meta)
            WHERE state = $status
            AND air_date <= CURDATE()
            ORDER BY air_date DESC, id_tvshow ASC
            LIMIT 1;
            SELECT * FROM shows_meta
            LEFT JOIN shows as s ON (s.id_tvshow = shows_meta.id_tvshow)
            WHERE id_meta = @update_id
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

    public static function QueryCandidate ($status)
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
            UPDATE shows_meta SET state = 3, worker_ip = '$remote_addr', id_meta = (SELECT @update_id := id_meta)
            WHERE state = $status
            AND air_date <= CURDATE()
            ORDER BY priority DESC, air_date DESC, id_tvshow ASC
            LIMIT 1;
            SELECT * FROM shows_meta
            LEFT JOIN shows as s ON (s.id_tvshow = shows_meta.id_tvshow)
            WHERE id_meta = @update_id
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
}
