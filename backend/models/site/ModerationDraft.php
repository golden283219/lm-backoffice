<?php

namespace backend\models\site;

use common\models\site\ModerationDraft as ModerationDraftSite;

class ModerationDraft extends ModerationDraftSite {

  const CATEGORY_MOVIES = 0;
  const CATEGORY_TVSHOWS = 1;
  const CATEGORY_TVEPISODES = 2;

  const STATUS_EXECUTED = 1;
  const STATUS_CANCELED = 2;
  const STATUS_WAITING = 0;
  
  public function getDraftItems()
  {
    return $this->hasMany(ModerationDraftItems::className(), ['id_moderation_draft' => 'id']);
  }

}