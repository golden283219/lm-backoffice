<?php

namespace console\models\imdb;

use console\models\imdb\dataset\ImdbAkasDataset;
use PDO;
use Yii;

/**
 * This is the model class for table "imdb_akas".
 *
 * @property int $id
 * @property string|null $titleId
 * @property int|null $ordering
 * @property string|null $title
 * @property string|null $region
 * @property string|null $language
 * @property string|null $types
 * @property string|null $attributes
 * @property string|null $isOriginalTitle
 */
class ImdbAkas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'imdb_akas';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
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
            [['ordering'], 'integer'],
            [['titleId', 'title', 'region', 'language', 'types', 'attributes', 'isOriginalTitle'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'titleId' => 'Title ID',
            'ordering' => 'Ordering',
            'title' => 'Title',
            'region' => 'Region',
            'language' => 'Language',
            'types' => 'Types',
            'attributes' => 'Attributes',
            'isOriginalTitle' => 'Is Original Title',
        ];
    }

    public static function updateDataset()
    {
        $dataset = new ImdbAkasDataset();

        $connection = new PDO(env('DB_IMDB_DSN'), env('DB_IMDB_USERNAME'), env('DB_IMDB_PASSWORD'));

        $connection->exec('DROP table imdb_akas_copy');
        $connection->exec("CREATE TABLE imdb_akas_copy LIKE imdb_akas;");

        $column_titles = "INSERT INTO `imdb_akas_copy`(`titleId`, `ordering`, `title`, `region`, `language`, `types`, `attributes`, `isOriginalTitle`)";

        $buffer = [];
        foreach ($dataset->read_line() as $line) {
			$titleId         = $line['titleId'];
			$ordering        = $line['ordering'];

			$title          = $line['title'];
			$title		    = str_replace("'", "\'", $title);
			$title		    = str_replace("\N", "", $title);

			$region         = str_replace("\N", "", $line['region']);
			$language       = str_replace("\N", "", $line['language']);
			$types          = str_replace("\N", "", $line['types']);

			$attributes     = str_replace("\N", "", $line['attributes']);
			$attributes	    = str_replace("'", "\'", $attributes);

			$isOriginalTitle = str_replace("\N", "", $line['isOriginalTitle']);
			$isOriginalTitle = ($isOriginalTitle) ? 1 : 0;

			$buffer[] = "('{$titleId}','{$ordering}','{$title}','{$region}','{$language}','{$types}','{$attributes}',{$isOriginalTitle})";

            if (count($buffer) > 7000) {
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

        $connection->exec('DROP TABLE imdb_akas;');
        $connection->exec('RENAME TABLE imdb_akas_copy TO imdb_akas;');
    }
}
