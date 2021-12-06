<?php

use yii\db\Migration;

/**
 * Class m190426_152529_add_movies_storage_index
 */
class m190426_152529_add_movies_storage_index extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createIndex('idx-movies_storage_id_movie', '{{%movies_storage}}', 'id_movie');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190426_152529_add_movies_storage_index cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190426_152529_add_movies_storage_index cannot be reverted.\n";

        return false;
    }
    */
}
