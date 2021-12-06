<?php

namespace console\models\imdb;

use console\models\imdb\dataset\ImdbBasicsDataset;
use PDO;
use Yii;

/**
 * This is the model class for table "imdb_basics".
 *
 * @property int $id
 * @property string $tconst
 * @property string|null $title_type
 * @property string|null $primary_title
 * @property string|null $original_title
 * @property int|null $is_adult
 * @property int|null $start_year
 * @property int|null $end_year
 * @property int|null $runtime_minutes
 * @property string|null $genres
 */
class ImdbBasics extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'imdb_basics';
    }

    /**
     * @return object|\yii\db\Connection|null
     * @throws \yii\base\InvalidConfigException
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
            [['is_adult', 'start_year', 'end_year', 'runtime_minutes'], 'integer'],
            [['genres'], 'safe'],
            [['primary_title', 'original_title'], 'string', 'max' => 610],
            [['tconst', 'title_type'], 'string', 'max' => 30],
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
            'title_type' => 'Title Type',
            'primary_title' => 'Primary Title',
            'original_title' => 'Original Title',
            'is_adult' => 'Is Adult',
            'start_year' => 'Start Year',
            'end_year' => 'End Year',
            'runtime_minutes' => 'Runtime Minutes',
            'genres' => 'Genres',
        ];
    }

    public static function updateDataset()
    {
        $dataset = new ImdbBasicsDataset();

        $connection = new PDO(env('DB_IMDB_DSN'), env('DB_IMDB_USERNAME'), env('DB_IMDB_PASSWORD'));

        $connection->exec('DROP table imdb_basics_copy');
        $connection->exec('CREATE TABLE imdb_basics_copy LIKE imdb_basics;');

        $column_titles = "INSERT INTO imdb_basics_copy (`tconst`, `title_type`, `primary_title`, `original_title`, `is_adult`, `genres`, `start_year`, `end_year`, `runtime_minutes`)";

        $buffer = [];
        foreach ($dataset->read_line() as $line) {
            $end_year = ($line['end_year'] != '\N') ? $line['end_year'] : 0;
            $start_year = ($line['start_year'] != '\N') ? $line['start_year'] : 0;
            $runtime_minutes = ($line['runtime_minutes'] != '\N') ? $line['runtime_minutes'] : 0;

            $genres = '{}';
            if ($line['genres'] != '') {
                $genres = str_replace("\N", "", $line['genres']);
                $genres = explode(',', preg_replace('/\s+/', '', $genres));
                $genres = json_encode($genres);
            }

            $primary_title = str_replace("'", "\'", $line['primary_title']);
            $original_title = str_replace("'", "\'", $line['original_title']);

            $buffer[] = "('{$line['tconst']}','{$line['title_type']}','{$primary_title}','{$original_title}','{$line['is_adult']}','{$genres}','{$start_year}','{$end_year}', '{$runtime_minutes}')";

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

        $connection->exec('DROP TABLE imdb_basics;');
        $connection->exec('RENAME TABLE imdb_basics_copy TO imdb_basics;');
    }
}
