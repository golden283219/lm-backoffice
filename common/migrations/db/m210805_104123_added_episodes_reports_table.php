<?php

use yii\db\Migration;

/**
 * Class m210805_104123_added_episodes_reports_table
 */
class m210805_104123_added_episodes_reports_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('shows_episodes_reports_cache', [
            'id'               => $this->primaryKey(),
            'count'            => $this->integer(9),
            'episode_number'   => $this->integer(9),
            'season_number'    => $this->integer(9),
            'id_tvshow'        => $this->integer(9),
            'id_episode'       => $this->integer(9),
            'last_reported_at' => $this->dateTime()->null(),
            'assigned_user_id' => $this->integer(),
            'is_closed'        => $this->tinyInteger()
        ]);

        $this->createIndex('shows_episodes_reports_cache-id_episode', 'shows_episodes_reports_cache', 'id_episode');
    }

    public function safeDown()
    {
        $this->dropTable('shows_episodes_reports_cache');
    }
}
