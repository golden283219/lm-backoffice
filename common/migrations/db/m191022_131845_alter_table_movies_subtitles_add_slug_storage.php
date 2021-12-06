<?php

use yii\db\Migration;

/**
 * Class m191022_131845_alter_table_movies_subtitles_add_slug_storage
 */
class m191022_131845_alter_table_movies_subtitles_add_slug_storage extends Migration
{
    public function safeUp()
    {

        $this->addColumn('movies_subtitles', 'shard', 'varchar(180) default null');
        return true;
    }

    public function safeDown()
    {
        $this->dropColumn('movies_subtitles', 'shard');
        return true;
    }
}
