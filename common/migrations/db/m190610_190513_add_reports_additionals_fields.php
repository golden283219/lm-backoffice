<?php

use yii\db\Migration;

/**
 * Class m190610_190513_add_reports_additionals_fields
 */
class m190610_190513_add_reports_additionals_fields extends Migration
{

  public function safeUp()
  {

    $this->addColumn('movies_reports', 'country', 'varchar(120)');
    $this->addColumn('movies_reports', 'ip_addr', 'bigint NOT NULL default 2130706433');
    $this->addColumn('movies_reports', 'ua', 'varchar(255)');
    $this->addColumn('movies_reports', 'fe_server', 'varchar(12)');
    $this->addColumn('movies_reports', 'iso', 'varchar(12)');
    $this->addColumn('movies_reports', 'os', 'varchar(50)');
    $this->addColumn('movies_reports', 'browser', 'varchar(50)');

    $this->addColumn('shows_reports', 'country', 'varchar(120)');
    $this->addColumn('shows_reports', 'ip_addr', 'bigint NOT NULL default 2130706433');
    $this->addColumn('shows_reports', 'ua', 'varchar(255)');
    $this->addColumn('shows_reports', 'fe_server', 'varchar(12)');
    $this->addColumn('shows_reports', 'iso', 'varchar(12)');
    $this->addColumn('shows_reports', 'os', 'varchar(50)');
    $this->addColumn('shows_reports', 'browser', 'varchar(50)');

  }

  public function safeDown()
  {

    $this->dropColumn('movies_reports', 'country');
    $this->dropColumn('movies_reports', 'ip_addr');
    $this->dropColumn('movies_reports', 'ua');
    $this->dropColumn('movies_reports', 'fe_server');
    $this->dropColumn('movies_reports', 'iso');
    $this->dropColumn('movies_reports', 'os');
    $this->dropColumn('movies_reports', 'browser');

    $this->dropColumn('shows_reports', 'country');
    $this->dropColumn('shows_reports', 'ip_addr');
    $this->dropColumn('shows_reports', 'ua');
    $this->dropColumn('shows_reports', 'fe_server');
    $this->dropColumn('shows_reports', 'iso');
    $this->dropColumn('shows_reports', 'os');
    $this->dropColumn('shows_reports', 'browser');

  }

}
