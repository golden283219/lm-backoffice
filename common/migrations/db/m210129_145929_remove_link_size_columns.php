<?php

use yii\db\Migration;

/**
 * Class m210129_144929_alter_link_column
 */

class m210129_145929_remove_link_size_columns extends Migration
{
    /**
     * {@inheritDoc}
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
        $this->dropColumn('shows_meta', 'link');
        $this->dropColumn('shows_meta', 'size');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210129_144929_alter_link_column cannot be reverted.\n";

        return false;
    }

}
