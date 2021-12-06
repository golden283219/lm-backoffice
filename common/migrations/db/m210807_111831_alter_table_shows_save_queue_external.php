<?php

use yii\db\Migration;

/**
 * Class m210807_111831_alter_table_shows_save_queue_external
 */
class m210807_111831_alter_table_shows_save_queue_external extends Migration
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
        $this->alterColumn('shows_save_queue_external', 'original_language', $this->string(80)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('shows_save_queue_external', 'original_language', $this->string(255)->notNull());
    }
}
