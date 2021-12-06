<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%imdb_crew}}`.
 */
class m210114_074638_create_imdb_crew_table extends Migration
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
        $this->createTable('{{%imdb_crew}}', [
            'id'            => $this->primaryKey(),
            'tconst'        => $this->string('30')->notNull(),
            'directors'     => $this->string(),
            'writers'       => $this->string(),
        ]);

        $this->createIndex('tconst-idx-imdb-crew', 'imdb_crew', 'tconst');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
         // drops index for column `tconst`
        $this->dropIndex(
            'tconst-idx-imdb-crew',
            'imdb_crew'
        );

        $this->dropTable('{{%imdb_crew}}');
    }
}
