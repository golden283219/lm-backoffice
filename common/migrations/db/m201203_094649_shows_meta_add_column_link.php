<?php

use yii\db\Migration;

/**
 * Class m201203_094649_shows_meta_add_column_link
 */
class m201203_094649_shows_meta_add_column_link extends Migration
{
    /**
     * {@inheritdoc}
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
        $this->addColumn('shows_meta', 'link', "string");
        $this->addColumn('shows_meta', 'flag_quality', "int");
        $this->addColumn('shows_meta', 'size', "int");

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('shows_meta', 'link');
        $this->dropColumn('shows_meta', 'flag_quality');
        $this->dropColumn('shows_meta', 'size');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201203_094649_shows_meta_add_column_link cannot be reverted.\n";

        return false;
    }
    */
}
