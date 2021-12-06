<?php

namespace backend\models\site;

class MoviesReports extends \common\models\site\MoviesReports
{

    public static function close_all_tickets_by_id_movie ($id_movie)
    {
        $query = "
            UPDATE 
                movies_reports
            SET
                is_closed = 1
            WHERE
                id_movie = $id_movie
        ";

        $connection = \Yii::$app->getDb();
        $command = $connection->createCommand($query);

        return $command->execute();
    }

    public static function get_active_reports_grouped_by_movie_count($id_user)
    {
        $query = "
            SELECT 
                count(DISTINCT(mr.id_movie)) as total
            FROM 
                movies_reports AS mr
            WHERE
                mr.is_closed = 0
        ";

        $connection = \Yii::$app->getDb();
        $command = $connection->createCommand($query);
        $results = $command->queryOne();

        return $results['total'];
    }

    public static function get_active_reports_grouped_by_movie($id_user, $page = 1, $per_page = 20)
    {
        $offset = $per_page * ($page - 1);

        $query = "
            SELECT
                mr.*, 
                m.year, 
                m.title, 
                m.poster, 
                m.slug,
                m.imdb_id
            FROM
                (
                    SELECT 
                        DISTINCT(mr.id_movie),
                        mm.locked_by
                    FROM 
                        movies_reports AS mr
                    INNER JOIN movies_moderation AS mm
                    ON mr.id_movie = mm.id_movie
                    WHERE
                        mr.is_closed = 0
                    AND 
                        ( mm.locked_by = $id_user OR mm.locked_by IS NULL )
                    ORDER BY mm.locked_by DESC
                    LIMIT $per_page OFFSET $offset
                ) AS mr
            LEFT JOIN movies AS m ON m.id_movie = mr.id_movie
        ";

        $connection = \Yii::$app->getDb();
        $command = $connection->createCommand($query);

        return $command->queryAll();
        
    }
}
