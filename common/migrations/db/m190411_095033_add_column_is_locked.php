<?php

use yii\db\Migration;

/**
 * Class m190411_095033_add_column_is_locked
 */
class m190411_095033_add_column_is_locked extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->addColumn('movies_moderation', 'is_locked', 'smallint NOT NULL default 0');
        $this->addColumn('movies_moderation', 'locked_by', 'int');
        $this->addColumn('movies_moderation', 'locked_at', 'int');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropColumn('movies_moderation', 'is_locked');
        $this->dropColumn('movies_moderation', 'locked_by');
        $this->dropColumn('movies_moderation', 'locked_at');

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190411_095033_add_column_is_locked cannot be reverted.\n";

        return false;
    }
    */
}
