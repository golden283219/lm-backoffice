<?php

use yii\db\Migration;

/**
 * Handles the creation of table `movies_save_queue_external`.
 */
class m191018_201353_create_movies_save_queue_external_table extends Migration
{

    /**
     * Change Database for migration
     */
    public function init()
    {
        $this->db = 'db_queue';
        parent::init();
    }

    public function safeUp()
    {
        $this->createTable('{{%movies_save_queue_external}}', [
            'id' => $this->primaryKey(),
            'id_movie' => $this->integer(32)->notNull(),
            'worker_ip' => $this->string()->notNull(),
            'files' => $this->json()->notNull(),
            'rel_title' => $this->string()->notNull(),
            'slug' => $this->string()->notNull(),
            'storage_slug' => $this->string()->notNull(),
            'status' => $this->integer(9)->defaultValue(0),
            'is_dd' => $this->integer(9)->defaultValue(0),
            'flag_quality' => $this->integer(9)->defaultValue(0),
            'size_bytes' => $this->bigInteger()->defaultValue(0),
            'os_hash' => $this->string(),
            'id_process' => $this->integer(11)->notNull(),
            'lang_iso_code' => $this->string()->notNull(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('NOW()')->append('ON UPDATE NOW()')
        ]);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%movies_save_queue_external}}');

        return true;
    }

}
