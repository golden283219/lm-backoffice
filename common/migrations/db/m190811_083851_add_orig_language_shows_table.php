<?php

use yii\db\Migration;

/**
 * Class m190811_083851_add_orig_language_shows_table
 */
class m190811_083851_add_orig_language_shows_table extends Migration
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
        $this->addColumn('shows', 'original_language', "varchar(255) DEFAULT 'en'");
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('shows', 'original_language');
        return true;
    }
    
}
