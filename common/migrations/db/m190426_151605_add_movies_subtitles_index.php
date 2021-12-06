<?php

use yii\db\Migration;

/**
 * Class m190426_151605_add_movies_subtitles_index
 */
class m190426_151605_add_movies_subtitles_index extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createIndex('idx_movies_subtitles_id_movie', '{{%movies_subtitles}}', 'id_movie');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190426_151605_add_movies_subtitles_index cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190426_151605_add_movies_subtitles_index cannot be reverted.\n";

        return false;
    }
    */
}
