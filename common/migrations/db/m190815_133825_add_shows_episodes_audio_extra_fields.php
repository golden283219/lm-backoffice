<?php

use yii\db\Migration;

/**
 * Class m190815_133825_add_shows_episodes_audio_extra_fields
 */
class m190815_133825_add_shows_episodes_audio_extra_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('shows_episodes_audio', 'lang_iso_639', "varchar(3) DEFAULT 'en'");
        $this->addColumn('shows_episodes_audio', 'original', "int(3) DEFAULT 1");

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('shows_episodes_audio', 'lang_iso_639');
        $this->dropColumn('shows_episodes_audio', 'original');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190815_133825_add_shows_episodes_audio_extra_fields cannot be reverted.\n";

        return false;
    }
    */
}
