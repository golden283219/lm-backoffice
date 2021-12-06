<?php

use yii\db\Migration;

/**
 * Class m190412_095447_update_movies_subtitles
 */
class m190412_095447_update_movies_subtitles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->addColumn('movies_subtitles', 'is_approved', 'smallint NOT NULL default 0');
        $this->addColumn('movies_subtitles', 'is_moderated', 'smallint NOT NULL default 1');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
     
        $this->dropColumn('movies_subtitles', 'is_approved');
        $this->dropColumn('movies_subtitles', 'is_moderated');

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190412_095447_update_movies_subtitles cannot be reverted.\n";

        return false;
    }
    */
}
