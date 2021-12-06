<?php

namespace console\models\imdb;

use console\models\imdb\dataset\ImdbCrewDataset;
use PDO;
use Yii;

/**
 * This is the model class for table "imdb_crew".
 *
 * @property int $id
 * @property string $tconst
 * @property string|null $directors
 * @property string|null $writers
 */
class ImdbCrew extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'imdb_crew';
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
            [['directors', 'writers'], 'safe'],
            [['tconst'], 'string', 'max' => 30],
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
            'directors' => 'Directors',
            'writers' => 'Writers',
        ];
    }

    public static function updateDataset()
    {
        $dataset = new ImdbCrewDataset();

        $connection = new PDO(env('DB_IMDB_DSN'), env('DB_IMDB_USERNAME'), env('DB_IMDB_PASSWORD'));

        $connection->exec('DROP table imdb_crew_copy');
        $connection->exec("CREATE TABLE imdb_crew_copy LIKE imdb_crew;");

        $column_titles = "INSERT INTO `imdb_crew_copy` (`tconst`, `directors`, `writers`)";

        $buffer = [];
        foreach ($dataset->read_line() as $line) {
			$tconst		= $line['tconst'];
			$directors	= $line['directors'];
			$writers	= trim($line['writers']) !== '\N' ? $line['writers'] : null;

			# Column Values
			$buffer[] = "('{$tconst}','{$directors}','{$writers}')";

			if (count($buffer) > 9000) {
                $body = implode(',' . PHP_EOL, $buffer);
                $buffer = [];

                $sql = "$column_titles\n VALUES\n $body;\n";
                $connection->exec($sql);
            }
        }

        if (count($buffer) > 0) {
            $body = implode(',' . PHP_EOL, $buffer);
            $buffer = [];

            $sql = "$column_titles\n VALUES\n $body;\n";
            $connection->exec($sql);
        }

        $dataset->flushFiles();

        $connection->exec('DROP TABLE imdb_crew;');
        $connection->exec('RENAME TABLE imdb_crew_copy TO imdb_crew;');
    }
}
