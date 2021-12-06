<?php

use yii\db\Migration;

/**
 * Class m210129_144929_alter_link_column
 */
class m210129_144929_alter_link_column extends Migration
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
        $this->alterColumn('shows_meta', 'link', 'text');
        $this->alterColumn('shows_meta', 'size', 'bigint');
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
