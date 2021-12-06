<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%movies_cast}}`.
 */
class m210104_231356_create_movies_cast_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%movies_cast}}', [
            'id'       => $this->primaryKey(),
            'role'     => $this->string(20)->notNull()->defaultValue('actor'),
            'id_cast'  => $this->integer()->notNull(),
            'id_movie' => $this->integer()->notNull(),
            'hero'     => $this->string(200)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%movies_cast}}');
    }
}
