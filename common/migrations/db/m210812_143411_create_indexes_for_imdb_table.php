<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%indexes_for_imdb}}`.
 */
class m210812_143411_create_indexes_for_imdb_table extends Migration
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
        $this->createIndex('imdb_akas-tconst-index', 'imdb_akas', 'titleId');
        $this->createIndex('imdb_basics-tconst-index', 'imdb_basics', 'tconst');
        $this->createIndex('imdb_ratings-tconst-index', 'imdb_ratings', 'tconst');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('imdb_akas-tconst-index', 'imdb_akas');
        $this->dropIndex('imdb_basics-tconst-index', 'imdb_basics');
        $this->dropIndex('imdb_ratings-tconst-index', 'imdb_ratings');
    }
}
