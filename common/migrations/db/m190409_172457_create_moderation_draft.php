<?php

use yii\db\Migration;

/**
 * Class m190409_172457_create_moderation_draft
 */
class m190409_172457_create_moderation_draft extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable('{{%moderation_draft}}', [
            'id' => $this->primaryKey(),
            'id_media' => $this->integer(32)->notNull(),
            'title' => $this->string(255),
            'category' => $this->integer(9),
            'created_by' => $this->integer(32)->notNull(),
            'executed_by' => $this->integer(32),
            'status' => $this->integer(32)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            'idx-moderation_draft-id_media',
            'moderation_draft',
            'id_media'
        );

        $this->createTable('{{%moderation_draft_items}}', [
            'id' => $this->primaryKey(),
            'id_moderation_draft' => $this->integer(32)->notNull(),
            'data' => $this->text(),
            'controller' => $this->string(100)->notNull(),
            'action' => $this->string(100)->notNull()
        ]);

        $this->createIndex(
            'idx-moderation_draft-id_moderation_draft',
            'moderation_draft_items',
            'id_moderation_draft'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        
        $this->dropTable('{{%moderation_draft}}');
        $this->dropTable('{{%moderation_draft_items}}');

    }
    
}
