<?php


namespace console\models\imdb;

use Yii;
use yii\db\ActiveRecord;

class IMDbSqlDumper
{
    /**
     * @var
     */
    private $model;

    /**
     * @var int
     */
    private $insert_limit = 1000;

    /**
     * @var string
     */
    private $dump_path;

    /**
     * @var
     */
    private $f;

    /**
     * @var
     */
    private $db_name;

    /**
     * @var string
     */
    private $random_table_name;

    /**
     * @var string
     */
    private $table_name;

    /**
     * IMDbSqlDumper constructor.
     *
     * @param ActiveRecord $model
     */
    public function __construct(ActiveRecord $model)
    {
        $this->model = new $model();

        $this->dump_path = Yii::getAlias('@storage/runtime/'.md5(microtime()) . '.sql');

        $this->f = fopen($this->dump_path, 'w');
        $this->db_name = $model::getDb();
        $this->table_name = $this->model::tableName();
        $this->random_table_name = md5(microtime() .'_'. $this->model::tableName());
    }


}
