<?php

use yii\db\Migration;

/**
 * Class m191021_194514_add_subs_column_shows_external
 */
class m191021_194514_add_subs_column_shows_external extends Migration
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
        $this->addColumn('shows_save_queue_external', 'subs', "json DEFAULT NULL");

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('shows_save_queue_external', 'subs');

        return true;
    }
}
