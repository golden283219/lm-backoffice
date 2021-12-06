<?php

use yii\db\Migration;

/**
 * Class m210202_184937_episodes_meta_recover_removed_fields
 */
class m210202_184937_episodes_meta_recover_removed_fields extends Migration
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
        $this->addColumn('shows_meta', 'link', "text");
        $this->addColumn('shows_meta', 'size', "bigint");

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('shows_meta', 'link');
        $this->dropColumn('shows_meta', 'size');

        return false;
    }
}
