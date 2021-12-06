<?php

use yii\db\Migration;

/**
 * Class m190806_102257_shows_queue_add_column_data
 */
class m190806_102257_shows_queue_add_column_data extends Migration
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
    public
    function safeUp()
    {
        $this->addColumn('shows', 'data', 'mediumtext');

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public
    function safeDown()
    {
        $this->dropColumn('shows', 'data');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190806_102257_shows_queue_add_column_data cannot be reverted.\n";

        return false;
    }
    */
}
