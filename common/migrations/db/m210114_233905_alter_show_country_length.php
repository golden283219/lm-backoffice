<?php

use yii\db\Migration;

/**
 * Class m210114_233905_alter_show_country_length
 */
class m210114_233905_alter_show_country_length extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('shows', 'country', 'varchar(80)');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210114_233905_alter_show_country_length cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210114_233905_alter_show_country_length cannot be reverted.\n";

        return false;
    }
    */
}
