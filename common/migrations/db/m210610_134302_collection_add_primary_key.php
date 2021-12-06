<?php

use yii\db\Migration;

/**
 * Class m210610_134302_collection_add_primary_key
 */
class m210610_134302_collection_add_primary_key extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addPrimaryKey('collection-collection_id_pk', 'collection', 'collection_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210610_134302_collection_add_primary_key cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210610_134302_collection_add_primary_key cannot be reverted.\n";

        return false;
    }
    */
}
