<?php

namespace api\modules\v1\helpers;

use common\models\MoviesStorage;

class MoviesStorageHelper
{

  /**
   * Delete Storage Item Action
   */
  public function DeleteStorageItem ($data) {

    $storage_item = MoviesStorage::findOne($data['id_storage']);

    if ($storage_item) {

      return $storage_item->delete();

    }

    return false;

  }

}