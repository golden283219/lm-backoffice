<?php

use yii\db\Migration;

class m210708_125022_create_table_metadata_proxy_servers extends Migration
{
    public function init()
    {
        $this->db = 'db_queue';
        parent::init();
    }

    public function safeUp()
    {
        $this->createTable('metadata_scraper_proxy_servers', [
            'id'        => $this->primaryKey(),
            'proxy_url' => $this->string(255),
            'usages'    => $this->integer(9),
            'enabled'   => $this->tinyInteger()
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('metadata_scraper_proxy_servers');
    }
}
