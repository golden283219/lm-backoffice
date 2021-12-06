<?php

use yii\db\Migration;

/**
 * Class m200618_162249_episodes_table_add_chunk_fixed_field
 */
class m200618_162249_episodes_table_add_chunk_fixed_field extends Migration
{

    public function safeUp()
    {
        $this->addColumn('shows_episodes', 'chunks_5s_fixed', "smallint(2) DEFAULT 1");

        return true;
    }

    public function safeDown()
    {
        $this->dropColumn('shows_episodes', 'chunks_5s_fixed');

        return true;
    }

}
