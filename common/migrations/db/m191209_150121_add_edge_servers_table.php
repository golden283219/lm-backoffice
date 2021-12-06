<?php

use yii\db\Migration;

/**
 * Class m191209_150121_add_edge_servers_table
 */
class m191209_150121_add_edge_servers_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('fe_servers', [
            'id' => $this->primaryKey(),
            'ip' => $this->string(80)->notNull(),
            'domain' => $this->string(80)->notNull(),
            'status_check_url' => $this->string(150)->notNull(),
            'max_bw' => $this->integer(9)->notNull(),
            'is_enabled' => $this->integer(2)->defaultValue(1),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')
        ]);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('fe_servers');

        return true;
    }
}
