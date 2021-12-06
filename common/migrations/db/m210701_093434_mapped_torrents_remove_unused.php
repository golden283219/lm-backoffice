<?php

use yii\db\Migration;

/**
 * Class m210701_093434_mapped_torrents_remove_unused
 */
class m210701_093434_mapped_torrents_remove_unused extends Migration
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
        $this->dropColumn('torrents_registry', 'id_download');
        $this->dropColumn('torrents_registry', 'id_download_folder');

        $this->dropColumn('shows_meta_torrent_map', 'map');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210701_093434_mapped_torrents_remove_unused cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210701_093434_mapped_torrents_remove_unused cannot be reverted.\n";

        return false;
    }
    */
}
