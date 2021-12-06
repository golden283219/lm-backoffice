<?php

use yii\db\Migration;

/**
 * Class m200224_153730_add_movies_history_table
 */
class m200224_153730_add_movies_history_table extends Migration
{
    /**
     * Change Database for migration
     */
    public function init()
    {
        $this->db = 'db_queue';
        parent::init();
    }

    public function safeUp()
    {
        $this->createTable('movies_moderation_history', [
            'id' => $this->primaryKey(),
            'id_movie' => $this->integer(9),
            'title' => $this->string(255),
            'imdb_id' => $this->string(20),
            'year' => $this->integer(9),
            'priority' => $this->tinyInteger(),
            'original_language' => $this->string(3),
            'id_user' => $this->integer(9),
            'status' => $this->tinyInteger(),
            'data' => $this->json(),
            'guid' => $this->string(100),
            'type' => $this->tinyInteger(),
            'worker_ip' => $this->string(50),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')
        ]);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('movies_moderation_history');

        return true;
    }
}
