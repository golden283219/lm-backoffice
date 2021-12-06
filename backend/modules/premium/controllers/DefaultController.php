<?php

namespace backend\modules\premium\controllers;

use yii\web\Controller;

/**
 * Default controller for the `premium` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
