<?php

use yii\db\Migration;

/**
 * Handles the creation of table `yt_converters`.
 */
class m191012_112653_create_yt_converters_table extends Migration
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
        $this->createTable('yt_converters', [
            'id' => $this->primaryKey(),
            'ip' => $this->string(100),
            'server_name' => $this->string(255),
            'status_check_url' => $this->string(255),
            // 0 - shows; 1 - movies
            'type' => $this->smallInteger()->notNull()->defaultValue(0)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('yt_converters');
    }
}
