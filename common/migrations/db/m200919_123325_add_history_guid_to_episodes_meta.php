<?php

use yii\db\Migration;

/**
 * Class m200919_123325_add_history_guid_to_episodes_meta
 */
class m200919_123325_add_history_guid_to_episodes_meta extends Migration
{
    public function init()
    {
        $this->db = 'db_queue';
        parent::init();
    }

    public function safeUp()
    {
        $tableSchema = Yii::$app->db_queue->schema->getTableSchema('shows_meta');

        if (!isset($tableSchema->columns['history_guid'])) {
            $this->addColumn('shows_meta', 'history_guid', "varchar(100)");
        }

        return true;
    }

    public function safeDown()
    {
        $this->dropColumn('shows_meta', 'history_guid');

        return true;
    }
}
