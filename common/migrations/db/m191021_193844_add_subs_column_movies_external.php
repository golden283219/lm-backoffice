<?php

use yii\db\Migration;

/**
 * Class m191021_193844_add_subs_column_movies_external
 */
class m191021_193844_add_subs_column_movies_external extends Migration
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
        $this->addColumn('movies_save_queue_external', 'subs', "json DEFAULT NULL");

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('movies_save_queue_external', 'subs');

        return true;
    }
}
