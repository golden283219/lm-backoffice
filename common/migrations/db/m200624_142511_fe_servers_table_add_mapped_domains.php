<?php

use yii\db\Migration;

/**
 * Class m200624_142511_fe_servers_table_add_mapped_domains
 */
class m200624_142511_fe_servers_table_add_mapped_domains extends Migration
{
    public function init()
    {
        parent::init();
    }

    public function safeUp()
    {
        $this->addColumn('fe_servers', 'domain_mapped', "json");

        return true;
    }

    public function safeDown()
    {
        $this->dropColumn('fe_servers', 'domain_mapped');

        return true;
    }
}
