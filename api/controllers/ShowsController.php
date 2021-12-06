<?php declare(strict_types=1);

namespace api\controllers;

use Yii;
use yii\web\Controller;
use common\models\Shows;
use backend\models\queue\Shows as ShowsQueue;
use common\models\ShowsReports;

class ShowsController extends Controller
{

	public function behaviors()
	{
		return [
			'corsFilter' => [
				'class' => \yii\filters\Cors::className(),
			],
		];
	}
	
	public function actionReport (
		$title, 
		$id_episode,
		$id_show,
		$year,
		$slug,
		$episode,
		$season,
		$connection_probm,
		$sound_probm,
		$label_probm,
		$subs_probm,
		$video_probm,
		$message,
		$userEmail = ''
	)
    {
        $model = new ShowsReports();

        $model->id_show = $id_show;
        $model->year = $year;
        $model->slug = $slug;
        $model->season = $season;
        $model->title = $title;
        $model->video_probm = (int)$video_probm;
        $model->connection_probm = (int)$connection_probm;
        $model->sound_probm = (int)$sound_probm;
        $model->subs_probm = (int)$subs_probm;
        $model->label_probm = (int)$label_probm;
        $model->created_at = time();
        $model->episode = $episode;
        $model->user_email = $userEmail;
        $model->id_episode = $id_episode;
        $model->message = $message;

        if ($model->validate() && $model->save()) {
          return true;
        }

        return false;

    }

    public function actionSearch ($term = '')
    {

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return Shows::QueryTerm($term);

    }

    public function actionSearchQueue ($term = '')
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return ShowsQueue::QueryTerm($term);
    }

}
