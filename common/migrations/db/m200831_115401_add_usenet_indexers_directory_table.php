<?php

use yii\db\Migration;

/**
 * Class m200831_115401_add_usenet_indexers_directory_table
 */
class m200831_115401_add_usenet_indexers_directory_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('usenet_indexers_directory', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')
        ]);

        $this->createTable('usenet_indexers_accounts', [
            'id' => $this->primaryKey(),
            'usenet_indexers_directory_id' => $this->integer(),
            'login' => $this->string(255),
            'password' => $this->string(255),
            'api_key' => $this->string(255),
            'comment' => $this->text(),
        ]);

        return true;
    }

    public function safeDown()
    {
        $this->dropTable('usenet_indexers_directory');
        $this->dropTable('usenet_indexers_accounts');

        return true;
    }
}