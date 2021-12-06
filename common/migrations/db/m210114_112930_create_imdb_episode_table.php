<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%imdb_episode}}`.
 */
class m210114_112930_create_imdb_episode_table extends Migration
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
        $this->createTable('{{%imdb_episode}}', [
            'id' => $this->primaryKey(),
            'tconst'            => $this->string('30')->notNull(),
            'parentTconst'      => $this->string('30')->notNull(),
            'seasonNumber'      => $this->integer(),
            'episodeNumber'     => $this->integer()
        ]);

        $this->createIndex('tconst-idx-imdb-episode', 'imdb_episode', 'tconst');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops index for column `tconst`
        $this->dropIndex(
            'tconst-idx-imdb-episode',
            'imdb_episode'
        );

        $this->dropTable('{{%imdb_episode}}');
    }
}
