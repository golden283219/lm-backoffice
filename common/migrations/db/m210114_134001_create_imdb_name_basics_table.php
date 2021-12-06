<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%imdb_name_basics}}`.
 */
class m210114_134001_create_imdb_name_basics_table extends Migration
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
        $this->createTable('{{%imdb_name_basics}}', [
            'id' => $this->primaryKey(),
            'nconst' => $this->string(10)->notNull()->unique(),
            'primaryName' => $this->string(),
            'birthYear' => $this->string(4),
            'deathYear' => $this->string(4),
            'primaryProfession' => $this->text(),
            'knownForTitles' => $this->text(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%imdb_name_basics}}');
    }
}
