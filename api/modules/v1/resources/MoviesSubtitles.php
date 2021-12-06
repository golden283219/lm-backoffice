<?php

namespace api\modules\v1\resources;

use yii\helpers\Url;
use yii\web\Link;
use yii\web\Linkable;

class MoviesSubtitles extends \api\modules\v1\models\site\MoviesSubtitles implements Linkable
{
  public function fields()
  {
    return [
      'id', 
      'id_movie', 
      'url',
      'language',
      'is_approved',
      'is_moderated',
      'shard'
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
      Link::REL_SELF => Url::to(['movies/view', 'id' => $this->id_movie], true)
    ];
  }

}
