<?php

namespace api\modules\v1\resources;

use yii\helpers\Url;
use yii\web\Link;
use yii\web\Linkable;

class MoviesAudio extends \api\modules\v1\models\site\MoviesAudio implements Linkable
{
  public function fields()
  {
    return [
      'id', 
      'id_movie', 
      'shard',
      'storage_path',
      'type',
      'lang_iso_code'
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
      Link::REL_SELF => Url::to(['movies-audio/view', 'id' => $this->id_movie], true)
    ];
  }

}
