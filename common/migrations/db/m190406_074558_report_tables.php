<?php

use yii\db\Migration;

/**
 * Class m190406_074558_report_tables
 */
class m190406_074558_report_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable('{{%movies_reports}}', [
            'id' => $this->primaryKey(),
            'id_movie' => $this->integer(32)->notNull(),
            'sound_probm' => $this->smallInteger(1),
            'connection_probm' => $this->smallInteger(1),
            'label_probm' => $this->smallInteger(1),
            'video_probm' => $this->smallInteger(1),
            'subs_probm' => $this->smallInteger(1),
            'user_email' => $this->string(200),
            'slug' => $this->string(200),
            'title' => $this->string(200),
            'year' => $this->integer(32),
            'message' => $this->text(),
            'created_at' => $this->integer(32),
            'id_user' => $this->integer(32),
            'notify_user' => $this->smallInteger(1)->defaultValue(0),
            'unseen' => $this->smallInteger(1)->defaultValue(1),
            'is_closed' => $this->smallInteger(1)->defaultValue(0)
        ]);

        $this->createTable('{{%shows_reports}}', [
            'id' => $this->primaryKey(),
            'id_show' => $this->integer(32)->notNull(),
            'sound_probm' => $this->smallInteger(1),
            'connection_probm' => $this->smallInteger(1),
            'label_probm' => $this->smallInteger(1),
            'video_probm' => $this->smallInteger(1),
            'subs_probm' => $this->smallInteger(1),
            'slug' => $this->string(200),
            'title' => $this->string(200),
            'user_email' => $this->string(200),
            'year' => $this->integer(32),
            'id_episode' => $this->integer(32),
            'episode' => $this->integer(32),
            'season' => $this->integer(32),
            'message' => $this->text(),
            'id_user' => $this->integer(32),
            'created_at' => $this->integer(32),
            'unseen' => $this->smallInteger(1)->defaultValue(1),
            'notify_user' => $this->smallInteger(1)->defaultValue(0),
            'is_closed' => $this->smallInteger(1)->defaultValue(0)
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropTable('{{%movies_reports}}');
        $this->dropTable('{{%shows_reports}}');

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190406_074558_report_tables cannot be reverted.\n";

        return false;
    }
    */
}
