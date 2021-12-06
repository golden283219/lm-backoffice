<?php

namespace api\modules\v1;

use Yii;

class Module extends \yii\base\Module
{
  /** @var string */
  public $controllerNamespace = 'api\modules\v1\controllers';

  /**
   * @inheritdoc
   */
  public function init()
  {
    parent::init();
    Yii::$app->user->identityClass = 'api\modules\v1\models\ApiUserIdentity';
    Yii::$app->user->enableSession = false;
    Yii::$app->user->loginUrl = null;
  }

}
