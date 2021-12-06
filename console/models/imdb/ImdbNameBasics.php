<?php

namespace console\models\imdb;

use console\models\imdb\dataset\ImdbNameBasicsDataset;
use PDO;
use Yii;

/**
 * This is the model class for table "imdb_name_basics".
 *
 * @property int $id
 * @property string $nconst
 * @property string|null $primaryName
 * @property string|null $birthYear
 * @property string|null $deathYear
 * @property string|null $primaryProfession
 * @property string|null $knownForTitles
 */
class ImdbNameBasics extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'imdb_name_basics';
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
            [['nconst'], 'required'],
            [['primaryProfession', 'knownForTitles'], 'string'],
            [['nconst'], 'string', 'max' => 10],
            [['primaryName'], 'string', 'max' => 255],
            [['birthYear', 'deathYear'], 'string', 'max' => 4],
            [['nconst'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nconst' => 'Nconst',
            'primaryName' => 'Primary Name',
            'birthYear' => 'Birth Year',
            'deathYear' => 'Death Year',
            'primaryProfession' => 'Primary Profession',
            'knownForTitles' => 'Known For Titles',
        ];
    }

    public static function updateDataset()
    {
        $dataset = new ImdbNameBasicsDataset();

        $connection = new PDO(env('DB_IMDB_DSN'), env('DB_IMDB_USERNAME'), env('DB_IMDB_PASSWORD'));

        $connection->exec('DROP table imdb_name_basics_copy;');
        $connection->exec("CREATE TABLE imdb_name_basics_copy LIKE imdb_name_basics;");

        $column_titles = "INSERT INTO `imdb_name_basics_copy` (`nconst`, `primaryName`, `birthYear`, `deathYear`, `primaryProfession`, `knownForTitles`)";

        $buffer = [];
        foreach ($dataset->read_line() as $line) {
			$nconst				= $line['nconst'];
			$primaryName		= $line['primaryName'];
			$primaryName	 	= str_replace("'", "\'", $primaryName);
			$birthYear			= $line['birthYear'];
			$deathYear			= trim($line['deathYear']) !== '\N' ? $line['deathYear'] : null;

			$primaryProfession	= $line['primaryProfession'];
			$primaryProfession	= str_replace("'", "\'", $primaryProfession);

			$knownForTitles		= $line['knownForTitles'];
			$knownForTitles		= str_replace("'", "\'", $knownForTitles);

			# Column Values
			$buffer[] = "('{$nconst}','{$primaryName}','{$birthYear}','{$deathYear}','{$primaryProfession}','{$knownForTitles}')";

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

        $connection->exec('DROP TABLE imdb_name_basics;');
        $connection->exec('RENAME TABLE imdb_name_basics_copy TO imdb_name_basics;');
    }
}
