<?php

use yii\db\Migration;

/**
 * Class m190525_143117_add_movies_torrent_download
 */
class m190525_143117_add_movies_torrent_download extends Migration
{

  public function init()
  {
    $this->db = 'db_queue';
    parent::init();
  }

  public function safeUp()
  {
    $this->addColumn('movies', 'torrent_blob', 'mediumblob');
    $this->addColumn('movies', 'type', 'tinyint not null default 0');
    $this->addColumn('movies', 'rel_title', 'string');
  }

  public function safeDown()
  {

    $this->dropColumn('movies', 'torrent_blob');
    $this->dropColumn('movies', 'type');
    $this->dropColumn('movies', 'rel_title');

  }

}
