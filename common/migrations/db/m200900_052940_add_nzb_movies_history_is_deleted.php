<?php

use yii\db\Migration;

/**
 * Class m200225_052940_add_nzb_movies_history_guid
 */
class m200900_052940_add_nzb_movies_history_is_deleted extends Migration
{

    public function init()
    {
        $this->db = 'db_queue';
        parent::init();
    }

    public function safeUp()
    {
        $this->addColumn('movies_moderation_history', 'is_deleted', "smallint(2) DEFAULT 0");

        return true;
    }

    public function safeDown()
    {
        $this->dropColumn('movies_moderation_history', 'is_deleted');

        return true;
    }

}
