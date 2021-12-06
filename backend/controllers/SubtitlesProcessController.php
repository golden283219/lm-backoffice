<?php
/**
 * Created by PhpStorm.
 * User: dev136
 * Date: 17.04.2020
 * Time: 1:09
 */

namespace backend\controllers;

use Yii;
use yii\web\Controller;

class SubtitlesProcessController extends Controller
{
    const srt_to_vtt_bin = '/var/www/subtitles-process/bin.js';

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return true;
    }

    /**
     * Call nodejs script that convert subtitles, read response and send to user
     * @return string
     */
    public function actionIndex () {

        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->response->headers->set('Access-Control-Allow-Origin', '*');

        $response = '';

        if (isset($_FILES['srt']) && file_exists($_FILES['srt']['tmp_name'])) {
            $response = shell_exec($this::srt_to_vtt_bin . ' ' . $_FILES['srt']['tmp_name']);
        }

        return $response;

    }
}