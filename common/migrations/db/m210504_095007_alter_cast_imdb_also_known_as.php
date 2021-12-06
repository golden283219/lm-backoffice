<?php

use yii\db\Migration;

/**
 * Class m210504_095007_alter_cast_imdb_also_known_as
 */
class m210504_095007_alter_cast_imdb_also_known_as extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('cast_imdb', 'also_known_as', 'text');

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('cast_imdb', 'also_known_as', 'varchar(255)');

        return true;
    }
}
