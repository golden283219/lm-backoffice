<?php

use yii\db\Migration;

/**
 * Class m210104_220818_rename_movies_actors_info_to_cast_imdb
 */
class m210104_220818_rename_movies_actors_info_to_cast_imdb extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameTable('movies_actors_info', 'cast_imdb');

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameTable('cast_imdb', 'movies_actors_info');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210104_220818_rename_movies_actors_info_to_cast_imdb cannot be reverted.\n";

        return false;
    }
    */
}
