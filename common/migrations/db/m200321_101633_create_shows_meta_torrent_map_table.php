<?php

use yii\db\Migration;

/**
 * Handles the creation of table `shows_meta_torrent_map`.
 */
class m200321_101633_create_shows_meta_torrent_map_table extends Migration
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
        $this->createTable('shows_meta_torrent_map', [
            'id' => $this->primaryKey(),
            'id_meta' => $this->integer(9),
            'status' => $this->smallInteger(1),
            'id_torrents_registry' => $this->integer(9),
            'map' => $this->json()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('shows_meta_torrent_map');
    }
}
