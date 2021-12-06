<?php

namespace api\modules\v1\resources;


class MoviesStorage extends \api\modules\v1\models\site\MoviesStorage
{

  public function fields()
  {
    return [
      'id', 
      'id_movie', 
      'url', 
      'quality',
      'is_converted',
    ];
  }

  public function GetMoviesStorage () {

    $SQL = 'SELECT movies.id_movie, shard_url, url, movies_storage.id as id_storage, movies.date_added FROM movies LEFT JOIN movies_storage ON movies_storage.id_movie = movies.id_movie ORDER BY date_added DESC';

    $connection = \Yii::$app->getDb();
    $command = $connection->createCommand($SQL);
    $result = $command->queryAll();

    $connection->close();

    return $result;

  }

}
