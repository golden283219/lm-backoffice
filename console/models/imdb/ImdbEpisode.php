<?php

namespace console\models\imdb;

use console\models\imdb\dataset\ImdbEpisodeDataset;
use PDO;
use Yii;

/**
 * This is the model class for table "imdb_episode".
 *
 * @property int $id
 * @property string $tconst
 * @property string $parentTconst
 * @property int|null $seasonNumber
 * @property int|null $episodeNumber
 */
class ImdbEpisode extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'imdb_episode';
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
            [['tconst', 'parentTconst'], 'required'],
            [['seasonNumber', 'episodeNumber'], 'integer'],
            [['tconst', 'parentTconst'], 'string', 'max' => 30],
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
            'parentTconst' => 'Parent Tconst',
            'seasonNumber' => 'Season Number',
            'episodeNumber' => 'Episode Number',
        ];
    }

    public static function updateDataset()
    {
        $dataset = new ImdbEpisodeDataset();

        $connection = new PDO(env('DB_IMDB_DSN'), env('DB_IMDB_USERNAME'), env('DB_IMDB_PASSWORD'));

        $connection->exec('DROP table imdb_episode_copy');
        $connection->exec("CREATE TABLE imdb_episode_copy LIKE imdb_episode;");

        $column_titles = "INSERT INTO `imdb_episode_copy` (`tconst`, `parentTconst`, `seasonNumber`, `episodeNumber`)";

        $buffer = [];
        foreach ($dataset->read_line() as $line) {
			$tconst			= $line['tconst'];

			$parentTconst	= $line['parentTconst'];
			$parentTconst   = str_replace("\N", "", $line['parentTconst']);

			$seasonNumber	= $line['seasonNumber'];
			$seasonNumber   = str_replace("\N", "", $line['seasonNumber']);
			$seasonNumber   = (trim($seasonNumber) != '') ? (int)$seasonNumber : 0;

			$episodeNumber	= $line['episodeNumber'];
			$episodeNumber  = str_replace("\N", "", $line['episodeNumber']);
			$episodeNumber  = (trim($episodeNumber) != '') ? (int) $episodeNumber : 0;

			# Column Values
            $buffer[] = "('{$tconst}','{$parentTconst}',{$seasonNumber},{$episodeNumber})";

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

        $connection->exec('DROP TABLE imdb_episode;');
        $connection->exec('RENAME TABLE imdb_episode_copy TO imdb_episode;');
    }
}
