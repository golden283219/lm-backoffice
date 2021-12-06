<?php

use yii\db\Migration;

/**
 * Class m210610_135451_add_missing_primary_key_homepage_data
 */
class m210610_135451_add_missing_primary_key_homepage_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addPrimaryKey('home_page_data-id_pk', 'home_page_data', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210610_135451_add_missing_primary_key_homepage_data cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210610_135451_add_missing_primary_key_homepage_data cannot be reverted.\n";

        return false;
    }
    */
}
