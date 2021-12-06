<?php

use yii\db\Migration;

/**
 * Class m190424_225936_add_shows_moderation_list
 */
class m190424_225936_add_shows_moderation_list extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->addColumn('shows_episodes', 'is_locked', 'smallint NOT NULL default 0');
        $this->addColumn('shows_episodes', 'quality_approved', 'smallint NOT NULL default 0');
        $this->addColumn('shows_episodes', 'finalized_subs', 'smallint NOT NULL default 0');
        $this->addColumn('shows_episodes', 'have_all_subs', 'smallint NOT NULL default 0');
        $this->addColumn('shows_episodes', 'missing_languages', 'smallint NOT NULL default 0');
        $this->addColumn('shows_episodes', 'subs_count', 'smallint NOT NULL default 0');
        $this->addColumn('shows_episodes', 'locked_by', 'int');
        $this->addColumn('shows_episodes', 'locked_at', 'int');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropColumn('shows_episodes', 'is_locked');
        $this->dropColumn('shows_episodes', 'quality_approved');
        $this->dropColumn('shows_episodes', 'finalized_subs');
        $this->dropColumn('shows_episodes', 'have_all_subs');
        $this->dropColumn('shows_episodes', 'missing_languages');
        $this->dropColumn('shows_episodes', 'locked_by');
        $this->dropColumn('shows_episodes', 'locked_at');
        $this->dropColumn('shows_episodes', 'subs_count');

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190424_225936_add_shows_moderation_list cannot be reverted.\n";

        return false;
    }
    */
}
