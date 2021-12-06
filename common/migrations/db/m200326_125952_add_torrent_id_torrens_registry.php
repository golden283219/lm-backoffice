<?php

use yii\db\Migration;

class m200326_125952_add_torrent_id_torrens_registry extends Migration
{
    public function init()
    {
        $this->db = 'db_queue';
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('torrents_registry', 'id_torrent', $this->string(100));

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('torrents_registry', 'id_torrent');

        return true;
    }
}
