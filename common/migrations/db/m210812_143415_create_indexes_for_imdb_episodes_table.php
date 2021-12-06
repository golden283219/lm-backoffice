<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%indexes_for_imdb}}`.
 */
class m210812_143415_create_indexes_for_imdb_episodes_table extends Migration
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
        $this->createIndex('imdb_episode-parentTconst-index', 'imdb_episode', 'parentTconst');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('imdb_episode-parentTconst-index', 'imdb_episode');
    }
}
