<?php

use yii\db\Migration;

/**
 * Class m190406_153727_create_users_action_logs
 */
class m190406_153727_create_users_action_logs extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable('{{%users_action_log}}', [
            'id' => $this->primaryKey(),
            'id_user' => $this->integer(32)->notNull(),
            'action' => $this->string(80),
            'category' => $this->string(80),
            'data' => $this->text(),
            'log_time' => $this->integer()->notNull(),
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        
        $this->dropTable('{{%users_action_log}}');

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190406_153727_create_users_action_logs cannot be reverted.\n";

        return false;
    }
    */
}
