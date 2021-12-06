<?php

use yii\db\Migration;

/**
 * Handles the creation of table `torrents_registry`.
 */
class m200321_110625_create_torrents_registry_table extends Migration
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
        $this->createTable('torrents_registry', [
            'id' => $this->primaryKey(),
            'type' => $this->smallInteger(1)->defaultValue(0),
            'torrent_contents' => $this->text(),
            'id_download' => $this->bigInteger(),
            'id_download_folder' => $this->bigInteger(),
            'status' => $this->smallInteger()->defaultValue(0)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('torrents_registry');
    }
}
