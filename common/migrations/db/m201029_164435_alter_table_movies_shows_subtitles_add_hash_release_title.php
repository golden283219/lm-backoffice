<?php

use yii\db\Migration;

/**
 * Class m201029_164435_alter_table_movies_subtitles_add_hash_release_title
 */
class m201029_164435_alter_table_movies_shows_subtitles_add_hash_release_title extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('movies_subtitles', 'hash', 'varchar(32) not null default "" ');
        $this->addColumn('movies_subtitles', 'release_title', 'varchar(100) not null default ""');

        $this->addColumn('shows_episodes_subtitles', 'hash', 'varchar(32) not null default "" ');
        $this->addColumn('shows_episodes_subtitles', 'release_title', 'varchar(100) not null default ""');

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('movies_subtitles', 'hash');
        $this->dropColumn('movies_subtitles', 'release_title');

        $this->dropColumn('shows_episodes_subtitles', 'hash');
        $this->dropColumn('shows_episodes_subtitles', 'release_title');

        return true;
    }
}
