<?php

use yii\db\Migration;

class m200624_155133_fe_servers_rename_doman_to_server_name extends Migration
{
    public function init()
    {
        parent::init();
    }

    public function safeUp()
    {
        $this->renameColumn('fe_servers', 'domain', 'server_name');

        return true;
    }

    public function safeDown()
    {
        $this->renameColumn('fe_servers', 'server_name', 'domain');

        return true;
    }
}
