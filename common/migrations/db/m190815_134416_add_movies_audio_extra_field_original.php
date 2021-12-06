<?php

use yii\db\Migration;

/**
 * Class m190815_134416_add_movies_audio_extra_field_original
 */
class m190815_134416_add_movies_audio_extra_field_original extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('movies_audio', 'original', "int(3) DEFAULT 1");

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('movies_audio', 'original');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190815_134416_add_movies_audio_extra_field_original cannot be reverted.\n";

        return false;
    }
    */
}
