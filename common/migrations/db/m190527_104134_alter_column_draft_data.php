<?php

use yii\db\Migration;

/**
 * Class m190527_104134_alter_column_draft_data
 */
class m190527_104134_alter_column_draft_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->alterColumn('moderation_draft_items', 'data', 'longtext');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190527_104134_alter_column_draft_data cannot be reverted.\n";

        return false;
    }

}
