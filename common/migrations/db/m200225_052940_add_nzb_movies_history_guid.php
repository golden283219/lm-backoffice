<?php

use yii\db\Migration;

/**
 * Class m200225_052940_add_nzb_movies_history_guid
 */
class m200225_052940_add_nzb_movies_history_guid extends Migration
{
    public function init()
    {
        $this->db = 'db_queue';
        parent::init();
    }

    public function safeUp()
    {
        $this->addColumn('movies', 'history_guid', "varchar(100)");

        return true;
    }

    public function safeDown()
    {
        $this->dropColumn('movies', 'history_guid');

        return true;
    }

}
