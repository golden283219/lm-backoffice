<?php

namespace api\modules\v1\resources;

use yii\helpers\Url;
use yii\web\Link;
use yii\web\Linkable;

class GarbageCollection extends \api\modules\v1\models\queue\GarbageCollection implements Linkable
{
  
  public function fields()
  {
    return [
      'id',
      'storage',
      'path',
      'date_added'
    ];
  }

  /**
     * Returns a list of links.
     *
     * @return array the links
     */
    public function getLinks()
    {
        return [
            Link::REL_SELF => Url::to(['garbage-collection/view', 'id' => $this->id], true)
        ];
    }

}
