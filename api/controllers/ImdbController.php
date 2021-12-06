<?php declare(strict_types=1);

namespace api\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;

class ImdbController extends Controller
{
    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
            ],
        ];
    }

    /**
     * @param $imdb_id
     *
     * @return array
     */
    public function actionBasicMetadata($imdb_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $page_contents = file_get_contents('https://www.imdb.com/title/' . $imdb_id);

        $original_language = imdb_find_original_language($page_contents);
        $original_language = !empty($original_language) ? $original_language : 'en';

        $meta = imdb_find_year_title($page_contents);
        $meta['original_language'] = $original_language;

        return $meta;
    }
}
