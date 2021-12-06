<?php

use yii\db\Migration;

/**
 * Class m210531_230318_change_title_type
 */
class m210531_230318_change_title_type extends Migration
{

    public function init()
    {
        $this->db = 'db_imdb';
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%imdb_akas}}', 'title', 'text');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210531_230318_change_title_type cannot be reverted.\n";

        return false;
    }
}
