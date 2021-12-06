<?php

namespace console\controllers;

use common\models\ShowsReports;
use common\models\site\ShowsEpisodesReportsCache;
use common\services\EpisodesQueueBuffer;
use yii\console\Controller;
use yii\db\Query;
use yii\helpers\Console;
use console\models\site\Shows as ShowsSite;
use console\models\queue\Shows as ShowsQueue;
use console\models\queue\ShowsMeta as ShowsMetaQueue;
use common\models\ImdbRatings;
use \GuzzleHttp\Message\Request;

class ShowsManagerController extends Controller
{

    protected $client;

    public function actionUpdateReportsCache()
    {
        foreach ((new Query())->select(['id_episode', 'id_show'])->from('shows_reports')->distinct()->where(['is_closed' => 0])->batch(100) as $reports) {
            foreach ($reports as $report) {
                $report = ShowsReports::find()->where(['id_episode' => $report['id_episode']])->orderBy(['created_at' => SORT_DESC])->asArray()->one();
                $count = ShowsReports::find()->where(['id_episode' => $report['id_episode'], 'is_closed' => 0])->count();

                $episodes_reports = ShowsEpisodesReportsCache::find()->where(['id_episode' => $report['id_episode'], 'is_closed' => '0'])->one();

                if (empty($episodes_reports)) {
                    $episodes_reports = new ShowsEpisodesReportsCache;
                    $episodes_reports->id_tvshow = $report['id_show'];
                    $episodes_reports->id_episode = $report['id_episode'];
                    $episodes_reports->episode_number = $report['episode'];
                    $episodes_reports->season_number = $report['season'];
                    $episodes_reports->is_closed = 0;
                }

                $episodes_reports->last_reported_at = date('Y-m-d H:i:s', $report['created_at']);
                $episodes_reports->count = $count;
                $episodes_reports->save();
            }
        }
    }

    /**
     * Update All Existing Ratings on TV Shows
     */
    public function actionUpdateRatings()
    {
        $updatedCount = 0;
        $missingCount = 0;

        foreach (ShowsSite::find()->each() as $ShowSite) {
            $IMDbRecord = ImdbRatings::find()->where([
                'tconst' => $ShowSite->imdb_id
            ])->asArray()->one();

            if ($IMDbRecord) {
                $ShowSite->imdb_rating = $IMDbRecord['averageRating'];
                if ($ShowSite->validate() && $ShowSite->save()) $updatedCount++;
            } else {
                $missingCount++;
            }
        }

        \Yii::info([
            'Action' => 'UpdateRatings',
            'Description' => 'Update All TVShows IMDb rating on site.',
            'Status' => 'Completed!',
            'UpdatedCount' => $updatedCount,
            'MissingCount' => $missingCount
        ], 'ShowsManager');

        Console::output('Done.');
    }

    /**
     * Set All Not Found Episodes to check for download again.
     */
    public function actionResetFailedEpisodes()
    {
        $resetCount = ShowsMetaQueue::ResetFailedEpisodes(env('EPISODES_QUEUE_MISSING', 4), env('EPISODES_QUEUE_FRESH', 0));

        Console::output('actionResetFailedEpisodes Done. Reset Count: ' . $resetCount);

        \Yii::info([
            'Action' => 'ResetFailedEpisodes',
            'Description' => 'Set All Not Found Episodes to check for download again',
            'Status' => 'Completed!',
            'Count' => $resetCount,
        ], 'ShowsManager');

        return true;
    }

    /**
     * Put items in queue
     *
     * @param int $limit
     */
    public function actionValidateQueueBuffer($limit = 10)
    {
        (new EpisodesQueueBuffer)->fillJobsQueue($limit);
        Console::output('Success');
    }


}
