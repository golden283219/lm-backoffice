<?php

use yii\db\Migration;

/**
 * Class m190924_200434_create_shows_save_queue_table_youtube
 */
class m190924_200434_create_shows_save_queue_table_youtube extends Migration
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
        $this->createTable('{{%shows_save_queue_external}}', [
            'id' => $this->primaryKey(),
            'id_tvshow' => $this->integer(32)->notNull(),
            'id_episode' => $this->integer(32)->notNull(),
            'episode' => $this->integer(32)->notNull(),
            'season' => $this->integer(32)->notNull(),
            'remote_ip' => $this->string()->notNull(),
            'files' => $this->json()->notNull(),
            'original_language' => $this->string()->notNull(),
            'rel_title' => $this->string()->notNull(),
            'slug' => $this->string()->notNull(),
            'status' => $this->integer(9)->defaultValue(0),
            'is_dd' => $this->integer(9)->defaultValue(0),
            'flag_quality' => $this->integer(9)->defaultValue(0),
            'id_process' => $this->integer(11)->notNull(),
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
        $this->dropTable('{{%shows_save_queue_external}}');

        return true;
    }
    
}
