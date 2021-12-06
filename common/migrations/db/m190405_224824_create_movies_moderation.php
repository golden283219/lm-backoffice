<?php

use yii\db\Migration;

/**
 * Class m190405_224824_create_movies_moderation
 */
class m190405_224824_create_movies_moderation extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable('{{%movies_moderation}}', [
            'id' => $this->primaryKey(),
            'id_movie' => $this->integer(32)->notNull(),
            'quality_approved' => $this->smallInteger()->notNull()->defaultValue(0),
            'finalized_subs' => $this->smallInteger()->notNull()->defaultValue(0),
            'have_all_subs' => $this->smallInteger()->notNull()->defaultValue(0),
            'missing_languages' => $this->json()
        ]);

        $this->createIndex(
            'idx-movies_moderation-id_movie',
            'movies_moderation',
            'id_movie'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%movies_moderation}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190405_224824_create_movies_moderation cannot be reverted.\n";

        return false;
    }
    */
}
