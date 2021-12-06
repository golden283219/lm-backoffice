<?php

use yii\db\Migration;

/**
 * Class m190813_090211_add_save_queue_fields_is_dd_original_language
 */
class m190813_090211_add_save_queue_fields_is_dd_original_language extends Migration
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
        $this->addColumn('shows_save_queue', 'original_language', "varchar(3) DEFAULT 'en'");
        $this->addColumn('shows_save_queue', 'is_dd', "int DEFAULT 0");

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('shows_save_queue', 'original_language');
        $this->dropColumn('shows_save_queue', 'is_dd');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190813_090211_add_save_queue_fields_is_dd_original_language cannot be reverted.\n";

        return false;
    }
    */
}
