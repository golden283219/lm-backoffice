<?php

use yii\db\Migration;

/**
 * Class m201111_165250_nzb_shows_add_thetvdb_field
 */
class m201111_165250_nzb_shows_add_thetvdb_field extends Migration
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

        if (!isset($tableSchema->columns['tvdb_id'])) {
            $this->addColumn('shows', 'tvdb_id', "int");
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('shows', 'tvdb_id');

        return false;
    }
}
