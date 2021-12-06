<?php

use yii\db\Migration;

class m210730_122259_added_new_map_field extends Migration
{
    public function init()
    {
        $this->db = 'db_queue';
        parent::init();
    }

    public function safeUp()
    {
        $this->addColumn('shows_meta', 'map', $this->string(500)->null());
    }

    public function safeDown()
    {
        $this->dropColumn('shows_meta', 'map');
    }
}
