<?php

namespace console\models\imdb;

use console\models\imdb\dataset\ImdbPrincipalsDataset;
use PDO;
use Yii;

/**
 * This is the model class for table "imdb_principal".
 *
 * @property int $id
 * @property string $tconst
 * @property int|null $ordering
 * @property string|null $nconst
 * @property string|null $category
 * @property string|null $job
 * @property string|null $characters
 */
class ImdbPrincipal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'imdb_principal';
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
            [['ordering'], 'integer'],
            [['tconst'], 'string', 'max' => 30],
            [['nconst', 'category', 'job', 'characters'], 'string', 'max' => 255],
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
            'ordering' => 'Ordering',
            'nconst' => 'Nconst',
            'category' => 'Category',
            'job' => 'Job',
            'characters' => 'Characters',
        ];
    }

    public static function updateDataset()
    {
        $dataset = new ImdbPrincipalsDataset();

        $connection = new PDO(env('DB_IMDB_DSN'), env('DB_IMDB_USERNAME'), env('DB_IMDB_PASSWORD'));

        $connection->exec('DROP table imdb_principal_copy;');
        $connection->exec("CREATE TABLE imdb_principal_copy LIKE imdb_principal;");

        $column_titles = "INSERT INTO `imdb_principal_copy` (`tconst`, `ordering`, `nconst`, `category`, `job`, `characters`)";

	    $buffer = [];
        foreach ($dataset->read_line() as $line) {
			$tconst     = $line['tconst'];
			$ordering   = $line['ordering'];
			$nconst     = $line['nconst'];
			$category   = $line['category'];

			$job        = $line['job'];
			$job	    = str_replace("\N", "", $job);
			$job        = str_replace("'", "\'", $job);

			$characters = $line['characters'];
			$characters = str_replace("\N", "", $characters);
			$characters = str_replace("'", "\'", $characters);

			# Column Values
			$buffer[] = "('{$tconst}','{$ordering}','{$nconst}','{$category}','{$job}','{$characters}')";

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

        $connection->exec('DROP TABLE imdb_principal;');
        $connection->exec('RENAME TABLE imdb_principal_copy TO imdb_principal;');
    }
}
