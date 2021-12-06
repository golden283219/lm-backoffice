<?php


namespace console\models;

use yii\db\Exception;

class MetadataScraperProxyServers extends \common\models\MetadataScraperProxyServers
{
    public static function queryMostAvailable()
    {
        $connection = self::getConnection();

        if ($connection->connect_error) {
            throw new Exception("Cannot connect to databse " . $connection->connect_error, 'Unable TO connect database', 0);
        }

        mysqli_set_charset($connection, 'utf8');

        $response = null;

        $connection->multi_query("
            SET @update_id := 0;
            UPDATE nzb.metadata_scraper_proxy_servers SET usages = usages + 1, id = (SELECT @update_id := id)
            WHERE enabled = 1
            ORDER BY usages ASC LIMIT 1;
            SELECT @update_id as id;
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
            return $response[0]['id'];
        }

        return $response;
    }

    private static function getConnection ()
    {
        $connection_credits = array();
        $connection = explode(':', env('DB_QUEUE_DSN'));

        $dsn_parts = explode(';', $connection['1']);

        foreach ($dsn_parts as $dsn_value) {
            $dsn_key_value = explode('=', $dsn_value);
            $connection_credits[$dsn_key_value['0']] = $dsn_key_value['1'];
        }

        return new \mysqli($connection_credits['host'], env('DB_QUEUE_USERNAME'), env('DB_QUEUE_PASSWORD'), $connection_credits['dbname'], $connection_credits['port']);
    }
}
