<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%imdb_principal}}`.
 */
class m210114_121836_create_imdb_principal_table extends Migration
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
        $this->createTable('{{%imdb_principal}}', [
            'id' => $this->primaryKey(),
            'tconst'         => $this->string('30')->notNull(),
            'ordering'       => $this->integer(),
            'nconst'         => $this->string(),
            'category'       => $this->string(),
            'job'            => $this->string(),
            'characters'     => $this->string(),
        ]);

        $this->createIndex('tconst-idx-imdb-principal', 'imdb_principal', 'tconst');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops index for column `tconst`
        $this->dropIndex(
            'tconst-idx-imdb-principal',
            'imdb_principal'
        );

        $this->dropTable('{{%imdb_principal}}');
    }
}
