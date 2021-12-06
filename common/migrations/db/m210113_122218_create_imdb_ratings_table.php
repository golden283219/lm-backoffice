<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%imdb_ratings}}`.
 */
class m210113_122218_create_imdb_ratings_table extends Migration
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
        $this->createTable('{{%imdb_ratings}}', [
            'id'                => $this->primaryKey(),
            'tconst'            => $this->string('30')->notNull(),
            'averageRating'     => $this->decimal(2, 1),
            'numVotes'          => $this->integer(),
        ]);

        $this->createIndex('tconst-idx-imdb-ratings', 'imdb_ratings', 'tconst');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops index for column `tconst`
        $this->dropIndex(
            'tconst-idx-imdb-ratings',
            'imdb_ratings'
        );

        $this->dropTable('{{%imdb_ratings}}');
    }
}
