<?php

namespace common\components\log;

use common\models\UsersActionLog as UsersActionLogModel;

class UsersActionLog {

	public function logAction ($action, $category, $data) {

		$model = new UsersActionLogModel();
		$model->id_user = \Yii::$app->user->identity->id;
		$model->action = $action;
		$model->log_time = time();
		$model->category = $category;
		$model->data = $data;

		if ($model->validate() && $model->save()) {

			return true;
			
		}

		return false;

	}
	
}