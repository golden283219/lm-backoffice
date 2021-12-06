<?php

namespace backend\modules\youtube\controllers;

use yii\web\Controller;

/**
 * Default controller for the `Youtube` module
 */
class VncController extends Controller
{

    public $layout = '/bold.php';
    
    /**
     * Link to given VNV
     */
    public function actionLink($link, $title)
    {
        return $this->render('link', [
            'title' => $title,
            'link' => $link
        ]);
    }
   
}
