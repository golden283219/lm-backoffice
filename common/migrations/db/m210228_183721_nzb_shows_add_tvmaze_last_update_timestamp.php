<?php

use yii\db\Migration;

/**
 * Class m210228_183721_nzb_shows_add_tvmaze_last_update_timestamp
 */
class m210228_183721_nzb_shows_add_tvmaze_last_update_timestamp extends Migration
{
    /**
     * {@inheritdoc}
     */
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
        $tableSchema = Yii::$app->db_queue->schema->getTableSchema('shows');

        if (!isset($tableSchema->columns['tvmaze_updated_timestamp'])) {
            $this->addColumn('shows', 'tvmaze_updated_timestamp', "int");
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('shows', 'tvmaze_updated_timestamp');

        return false;
    }
}
