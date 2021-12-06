<?php

use yii\db\Migration;

/**
 * Class m191201_204213_add_reports_fileds_for_movies_moderation
 */
class m191201_204213_add_reports_fileds_for_movies_moderation extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('movies_moderation', 'active_reports_count', "int(9) DEFAULT 0");
        $this->addColumn('movies_moderation', 'latest_reports_timestamp', "int(9) DEFAULT 0");

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('movies_moderation', 'active_reports_count');
        $this->dropColumn('movies_moderation', 'latest_reports_timestamp');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191201_204213_add_reports_fileds_for_movies_moderation cannot be reverted.\n";

        return false;
    }
    */
}
