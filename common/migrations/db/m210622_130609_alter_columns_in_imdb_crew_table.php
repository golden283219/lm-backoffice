<?php

use yii\db\Migration;

/**
 * Class m210622_130609_alter_columns_in_imdb_crew_table
 */
class m210622_130609_alter_columns_in_imdb_crew_table extends Migration
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
        $this->alterColumn('{{%imdb_crew}}', 'directors', $this->text());
        $this->alterColumn('{{%imdb_crew}}', 'writers', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%imdb_crew}}', 'directors', $this->string());
        $this->alterColumn('{{%imdb_crew}}', 'writers', $this->string());
    }
}
