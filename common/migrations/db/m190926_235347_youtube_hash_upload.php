<?php

use yii\db\Migration;

/**
 * Class m190926_235347_youtube_hash_upload
 */
class m190926_235347_youtube_hash_upload extends Migration
{
    /**
     * Change Database for migration
     */
    public function init()
    {
        $this->db = 'db_queue';
        parent::init();
    }

    public function safeUp()
    {
        $this->createTable('{{%yt_uploads}}', [
            'key' => $this->string(),
            'yt_link' => $this->string()->notNull()
        ]);

        $this->addPrimaryKey('key-yt_uploads_pk', 'yt_uploads', 'key');

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%yt_uploads}}');

        return true;
    }
}
