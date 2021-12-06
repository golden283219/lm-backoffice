<?php

use yii\db\Migration;

/**
 * Class m200730_104926_add_enabled_flag_to_fe_servers
 */
class m200730_104926_add_enabled_flag_to_fe_servers extends Migration
{
    public function init()
    {
        parent::init();
    }

    public function safeUp()
    {
        $this->addColumn('fe_servers', 'is_hidden', $this->tinyInteger()->defaultValue(1));

        return true;
    }

    public function safeDown()
    {
        $this->dropColumn('fe_servers', 'is_hidden');

        return true;
    }
}
