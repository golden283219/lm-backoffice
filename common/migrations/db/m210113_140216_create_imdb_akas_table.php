<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%imdb_akas}}`.
 */
class m210113_140216_create_imdb_akas_table extends Migration
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
        $this->createTable('{{%imdb_akas}}', [
            'id' => $this->primaryKey(),
            'titleId' => $this->string(),
            'ordering' => $this->integer(),
            'title' => $this->string(),
            'region' => $this->string(),
            'language' => $this->string(),
            'types' => $this->string(),
            'attributes' => $this->string(),
            'isOriginalTitle' => $this->boolean()->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%imdb_akas}}');
    }
}
