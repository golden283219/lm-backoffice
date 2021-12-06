<?php

use yii\db\Migration;

/**
 * Class m200618_162238_movies_table_add_chunk_fixed_field
 */
class m200618_162238_movies_table_add_chunk_fixed_field extends Migration
{

    public function safeUp()
    {
        $this->addColumn('movies', 'chunks_5s_fixed', "smallint(2) DEFAULT 1");

        return true;
    }

    public function safeDown()
    {
        $this->dropColumn('movies', 'chunks_5s_fixed');

        return true;
    }

}
