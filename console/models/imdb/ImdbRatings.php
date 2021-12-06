<?php

namespace console\models\imdb;

use console\models\imdb\dataset\ImdbRatingsDataset;
use PDO;
use Yii;

/**
 * This is the model class for table "imdb_ratings".
 *
 * @property int $id
 * @property string $tconst
 * @property float|null $averageRating
 * @property string|null $numVotes
 */
class ImdbRatings extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'imdb_ratings';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_imdb');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tconst'], 'required'],
            [['averageRating'], 'number'],
            [['tconst'], 'string', 'max' => 30],
            [['numVotes'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tconst' => 'Tconst',
            'averageRating' => 'Average Rating',
            'numVotes' => 'Num Votes',
        ];
    }

    public static function updateDataset()
    {
        $dataset = new ImdbRatingsDataset();

        $connection = new PDO(env('DB_IMDB_DSN'), env('DB_IMDB_USERNAME'), env('DB_IMDB_PASSWORD'));

        $connection->exec('DROP table imdb_ratings_copy');
        $connection->exec("CREATE TABLE imdb_ratings_copy LIKE imdb_ratings;");

        $column_titles = "INSERT INTO `imdb_ratings_copy`(`tconst`, `averageRating`, `numVotes`)";

        $buffer = [];
        foreach ($dataset->read_line() as $line) {
            $tconst = trim($line['tconst']);
            $averageRating = trim($line['averageRating']);
            $numVotes = trim($line['numVotes']);

            $buffer[] = "('{$tconst}','{$averageRating}','{$numVotes}')";

            if (count($buffer) > 9000) {
                $body = implode(',' . PHP_EOL, $buffer);
                $buffer = [];

                $sql = "$column_titles\n VALUES\n $body;\n";
                $connection->exec($sql);
            }
		}

        if (count($buffer) > 0) {
            $body = implode(',', $buffer);
            $buffer = [];
            $sql = "$column_titles\n VALUES\n $body;";

            $connection->exec($sql);
        }

        $dataset->flushFiles();

        $connection->exec('DROP TABLE imdb_ratings;');
        $connection->exec('RENAME TABLE imdb_ratings_copy TO imdb_ratings;');
    }
}
