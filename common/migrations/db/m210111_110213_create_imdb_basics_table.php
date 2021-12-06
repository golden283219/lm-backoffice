<?php

use yii\db\Migration;

class m210111_110213_create_imdb_basics_table extends Migration
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
        $this->createTable('imdb_basics', [
            'id'              => $this->primaryKey(),
            'tconst'          => $this->string('30')->notNull(),
            'title_type'      => $this->string('30'),
            'primary_title'   => $this->string('610'),
            'original_title'  => $this->string('610'),
            'is_adult'        => $this->tinyInteger()->defaultValue(0),
            'start_year'      => $this->integer(),
            'end_year'        => $this->integer(),
            'runtime_minutes' => $this->integer(),
            'genres'          => $this->json()
        ]);

        $this->createIndex('tconst-idx-imdb-basics', 'imdb_basics', 'tconst');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('imdb_basics');

        return true;
    }
}
