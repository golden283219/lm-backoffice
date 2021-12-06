<?php

use yii\db\Migration;

/**
 * Class m190814_150734_add_shows_save_queue_field_id_process
 */
class m190814_150734_add_shows_save_queue_field_id_process extends Migration
{

    /**
     * Change Database for migration
     */
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
        $this->addColumn('shows_save_queue', 'id_process', "int");

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('shows_save_queue', 'id_process');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190814_150734_add_shows_save_queue_field_id_process cannot be reverted.\n";

        return false;
    }
    */
}
