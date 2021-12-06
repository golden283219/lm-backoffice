<?php

use yii\db\Migration;

/**
 * Class m191110_142743_add_static_pages_table
 */
class m191110_142743_add_static_pages_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('static_pages', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255),
            'slug' => $this->string(100),
            'contents' => 'LONGTEXT',
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')
        ]);

        return true;
    }

    public function safeDown()
    {
        $this->dropTable('static_pages');

        return true;
    }
}
