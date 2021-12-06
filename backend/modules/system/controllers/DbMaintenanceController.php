<?php
/**
 * Author: Eugine Terentev <eugine@terentev.net>
 */

namespace backend\modules\system\controllers;

use yii\web\Controller;
use backend\models\site\MoviesReports;
use common\models\site\MoviesModeration;

class DbMaintenanceController extends Controller
{

    public $layout = '@backend/views/layouts/common';

    public function actionRebuildIndecies()
    {
        return 'hello worlds';
    }

    public function actionRebuildMoviesReportsIndecies()
    {
        $movies_ids = MoviesReports::find()
            ->select('id_movie')
            ->where(['is_closed' => 0])
            ->distinct()
            ->asArray()
            ->all();

        foreach ($movies_ids as $movie) {
            $latest_ts = '';
            $active_reports_count = MoviesReports::find()
                ->select('id_movie')
                ->where(['is_closed' => 0, 'id_movie' => $movie['id_movie']])
                ->count();
            var_dump($active_reports_count);
        }

        return 200;
    }
}
