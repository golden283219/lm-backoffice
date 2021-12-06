<?php declare(strict_types=1);

namespace api\controllers;

use HttpException;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class SiteController extends Controller
{
    public function actionIndex()
    {
        return $this->redirect(Yii::getAlias('@frontendUrl'));
    }

    public function actionReport(
        $title,
        $slug,
        $year,
        $connection_probm,
        $sound_probm,
        $label_probm,
        $subs_probm,
        $video_probm,
        $message,
        $id_movie,
        $userEmail = ''
    )
    {

        $model = new MoviesReports();
        $model->id_movie = (int)$id_movie;
        $model->message = $message;
        $model->user_email = $userEmail;
        $model->year = $year;
        $model->slug = $slug;
        $model->title = $title;
        $model->video_probm = (int)$video_probm;
        $model->connection_probm = (int)$connection_probm;
        $model->sound_probm = (int)$sound_probm;
        $model->subs_probm = (int)$subs_probm;
        $model->label_probm = (int)$label_probm;
        $model->created_at = time();

        if ($model->validate() && $model->save()) {

            return true;

        }

        return false;

    }

    public function actionError()
    {
        if (($exception = Yii::$app->getErrorHandler()->exception) === null) {
            $exception = new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        if ($exception instanceof HttpException) {
            Yii::$app->response->setStatusCode($exception->getCode());
        } else {
            Yii::$app->response->setStatusCode(500);
        }

        return $this->asJson(['error' => $exception->getMessage(), 'code' => $exception->getCode()]);
    }
}
