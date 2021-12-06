<?php

use yii\db\Migration;

/**
 * Class m201112_091208_media_db_shows_add_tvdb_column
 */
class m201112_091208_media_db_shows_add_tvdb_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('shows', 'tvdb_id', "int");

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
