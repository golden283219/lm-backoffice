<?php

use yii\db\Migration;

/**
 * Class m210520_084618_add_shows_meta_indexes
 */
class m210520_084618_add_shows_meta_indexes extends Migration
{
    public function init()
    {
        $this->db = 'db_queue';
        parent::init();
    }

    public function safeUp()
    {
        $this->createIndex(
            'idx-shows_meta-air_date',
            'shows_meta',
            'air_date'
        );

        $this->createIndex(
            'idx-shows_meta-worker_ip',
            'shows_meta',
            'worker_ip'
        );

        $this->createIndex(
            'idx-shows_meta-state',
            'shows_meta',
            'state'
        );
    }

    public function safeDown()
    {
        echo "m210520_084618_add_shows_meta_indexes cannot be reverted.\n";

        return true;
    }
}
