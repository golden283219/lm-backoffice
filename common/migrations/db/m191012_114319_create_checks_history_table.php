<?php

use yii\db\Migration;

/**
 * Handles the creation of table `checks_history`.
 */
class m191012_114319_create_checks_history_table extends Migration
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
        $this->createTable('checks_history', [
            'id' => $this->primaryKey(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')
        ]);

        $this->createTable('checks_history_servers', [
            'id' => $this->primaryKey(),
            'checks_history_id' => $this->integer(11)->notNull(),
            'ip' => $this->string(100),
            'server_name' => $this->string(255),
            'status' => $this->string(20),
            'message' => $this->string(255),
            'yt_account' => $this->string(255),
            'type' => $this->smallInteger()->notNull()->defaultValue(0)
        ]);

        $this->createIndex('idx_checks_history_id', '{{%checks_history_servers}}', 'checks_history_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('checks_history');
        $this->dropTable('checks_history_servers');
    }
}
