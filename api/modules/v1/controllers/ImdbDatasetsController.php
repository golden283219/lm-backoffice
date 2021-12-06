<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\imdb\ImdbBasics;
use api\modules\v1\models\imdb\ImdbEpisode;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\web\Response;

/**
 * Class MoviesController
 */
class ImdbDatasetsController extends ActiveController
{

    public $modelClass = '\api\modules\v1\models\imdb\ImdbBasics';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
        ];

        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formatParam' => 'o',
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
                'application/xml' => Response::FORMAT_XML,
            ],
        ];

        return $behaviors;
    }

    /**
     * @param $imdb_id
     * @return array
     * @throws \Exception
     */
    public function actionEpisodes($imdb_id)
    {
        $imdb_episode = ImdbEpisode::find()
            ->where(['parentTconst' => $imdb_id])
            ->orderBy(['seasonNumber' => SORT_ASC, 'episodeNumber' => SORT_ASC])
            ->all();

        $response = [];

        foreach ($imdb_episode as $episode) {
            if (empty($response[$episode->seasonNumber])) {
                $response[$episode->seasonNumber] = [];
            }

            $response[$episode->seasonNumber][] = [
                'seasonNumber' => $episode->seasonNumber,
                'episodeNumber' => $episode->episodeNumber,
                'title_type' => ArrayHelper::getValue($episode, 'basics.title_type'),
                'original_title' => ArrayHelper::getValue($episode, 'basics.original_title'),
                'primary_title' => ArrayHelper::getValue($episode, 'basics.primary_title'),
                'air_date' => ArrayHelper::getValue($episode, 'basics.start_year')
            ];
        }

        return $response;
    }

    /**
     * @param $imdb_id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function actionDetails($imdb_id)
    {
        $imdb_basics = ImdbBasics::find()->where(['tconst' => $imdb_id])->one();

        // grab original language
        if (empty($imdb_basics)) {
            return null;
        }

        $response = $imdb_basics->getAttributes();
        $response['akas'] = $imdb_basics->akas;
        $response['ratings'] = $imdb_basics->ratings;
        $response['original_language'] = ImdbBasics::getOriginalLanguage($imdb_id);

        return $response;
    }
}
