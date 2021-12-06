<?php

use yii\db\Migration;

/**
 * Class m210527_204652_shows_episodes_add_created_at
 */
class m210527_204652_shows_episodes_add_created_at extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('shows_episodes', 'created_at', $this->timestamp()->null()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('shows_episodes', 'updated_at', $this->timestamp()->null()->defaultExpression('CURRENT_TIMESTAMP')->append('ON UPDATE CURRENT_TIMESTAMP'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('shows_episodes', 'updated_at');
        $this->dropColumn('shows_episodes', 'created_at');

        return true;
    }
}
