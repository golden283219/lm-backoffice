<?php

use yii\db\Migration;

/**
 * Class m210621_085125_change_colums_imdb_ratings_table
 */
class m210621_085125_change_colums_imdb_ratings_table extends Migration
{

	public function init()
    {
        $this->db = 'db_imdb';
        parent::init();
	}

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%imdb_ratings}}', 'averageRating', $this->decimal(3,1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%imdb_ratings}}', 'averageRating', $this->decimal(2,1));
    }
}
