<?php

use yii\db\Migration;

/**
 * Class m201002_132603_prem_users_add_latest_transaction_field
 */
class m201002_132603_prem_users_add_latest_transaction_field extends Migration
{
    public function safeUp()
    {
        $this->addColumn('prem_users', 'latest_transaction_date', 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('prem_users', 'latest_transaction_date');

        return true;
    }
}
