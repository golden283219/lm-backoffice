<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%movies_actors_info}}`.
 */
class m201103_220011_create_movies_actors_info_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%movies_actors_info}}', [
            'id' => $this->primaryKey(),
            'imdb_actor_id' => $this->string(12),
            'birth_name' => $this->string(255),
            'birth_place' => $this->string(255),
            'birth_date' => $this->string(64),
            'photo' => $this->string(60),
            'bio' => 'LONGTEXT',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%movies_actors_info}}');
    }
}
