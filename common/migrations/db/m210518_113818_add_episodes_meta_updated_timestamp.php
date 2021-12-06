<?php

use yii\db\Migration;

/**
 * Class m210518_113818_add_episodes_meta_updated_timestamp
 */
class m210518_113818_add_episodes_meta_updated_timestamp extends Migration
{

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
        $this->addColumn('shows_meta', 'created_at', $this->timestamp()->null()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('shows_meta', 'updated_at', $this->timestamp()->append('ON UPDATE CURRENT_TIMESTAMP'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('shows_meta', 'updated_at');
        $this->dropColumn('shows_meta', 'created_at');

        return true;
    }
}
