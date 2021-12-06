<?php

use yii\db\Migration;

/**
 * Handles the creation of table `yt_accounts`.
 */
class m191011_181733_create_yt_accounts_table extends Migration
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
        $this->createTable('yt_accounts', [
            'id' => $this->primaryKey(),
            'yt_login' => $this->string(255),
            'yt_password' => $this->string(255),
            'yt_recovery_email' => $this->string(255),
            'in_use' => $this->smallInteger()->notNull()->defaultValue(0)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('yt_accounts');
    }
}
