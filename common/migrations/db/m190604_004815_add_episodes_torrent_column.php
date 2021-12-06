<?php

use yii\db\Migration;

/**
 * Class m190604_004815_add_episodes_torrent_column
 */
class m190604_004815_add_episodes_torrent_column extends Migration
{
  
  public function init()
  {
    $this->db = 'db_queue';
    parent::init();
  }

  public function safeUp()
  {
    $this->addColumn('shows_meta', 'torrent_blob', 'mediumblob');
    $this->addColumn('shows_meta', 'type', 'tinyint not null default 0');
    $this->addColumn('shows_meta', 'rel_title', 'string');
  }

  public function safeDown()
  {

    $this->dropColumn('shows_meta', 'torrent_blob');
    $this->dropColumn('shows_meta', 'type');
    $this->dropColumn('shows_meta', 'rel_title');

  }
  
}
