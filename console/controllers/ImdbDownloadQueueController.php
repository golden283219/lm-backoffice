<?php

namespace console\controllers;

use console\models\imdb\ImdbBasics;
use console\models\imdb\ImdbRatings;
use console\models\imdb\ImdbAkas;
use console\models\imdb\ImdbCrew;
use console\models\imdb\ImdbEpisode;
use console\models\imdb\ImdbPrincipal;
use console\models\imdb\ImdbNameBasics;

use Yii;
use yii\console\Controller;

class ImdbDownloadQueueController extends Controller
{
    private $temp_dir = '/tpm';

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->temp_dir = Yii::getAlias('@app/runtime');
    }

    private function scrapTitleBasics($database = 'imdb')
    {
        $this->stdout('Processing data in Imdb Basics' . PHP_EOL);
        ImdbBasics::updateDataset();
    }

    private function scrapTitleRatings($database = 'imdb')
    {
        $this->stdout('Processing data in Imdb Ratings' . PHP_EOL);
        ImdbRatings::updateDataset();
    }

    private function scrapTitleAkas($database = 'imdb')
    {
        $this->stdout('Processing data in Imdb Akas' . PHP_EOL);
        ImdbAkas::updateDataset();
    }

    private function scrapTitleCrew($database = 'imdb')
    {
        $this->stdout('Processing data in Imdb Crew' . PHP_EOL);
        ImdbCrew::updateDataset();
    }

    private function scrapTitleEpisode($database = 'imdb')
    {
        $this->stdout('Processing data in IMDB Episode ' . PHP_EOL);
        ImdbEpisode::updateDataset();
    }

    private function scrapTitlePrincipal($database = 'imdb')
    {
        $this->stdout('Processing data in IMDB Principal ' . PHP_EOL);
        ImdbPrincipal::updateDataset();
    }

    private function scrapNameBasics($database = 'imdb')
    {
        $this->stdout('Processing data in IMDB Name Basics ' . PHP_EOL);
        ImdbNameBasics::updateDataset();
    }

    public function actionUpdateDatasets($action = 'all', $database = 'imdb')
    {
        // Start
        $this->stdout('Starting...' . PHP_EOL);
        $start_date = Yii::$app->formatter->asDate('now', 'yyyy-MM-dd h:m:s');
        $this->stdout($start_date . PHP_EOL . PHP_EOL);

        switch ($action) {
            case 'title-basics':
                // Update Title Basics Dataset
                $this->scrapTitleBasics($database);
                break;
            case 'title-ratings':
                // Update Title Ratings Dataset
                $this->scrapTitleRatings($database);
                break;
            case 'title-akas':
                // Update Title Akas Dataset
                $this->scrapTitleAkas($database);
                break;
            case 'title-crew':
                // Update Title Crew Dataset
                $this->scrapTitleCrew($database);
                break;
            case 'title-episode':
                // Update Title Episode Dataset
                $this->scrapTitleEpisode($database);
                break;
            case 'title-principal':
                // Update Title Principal Dataset
                $this->scrapTitlePrincipal($database);
                break;
            case 'name-basics':
                // Update Name Basics Dataset
                $this->scrapNameBasics($database);
                break;
            default:
                // Update All Datasets
                $this->scrapTitleBasics($database);
                $this->scrapTitleRatings($database);
                $this->scrapTitleAkas($database);
                $this->scrapTitleCrew($database);
                $this->scrapTitleEpisode($database);
                $this->scrapTitlePrincipal($database);
                $this->scrapNameBasics($database);
        }

        // Finished
        $this->stdout(PHP_EOL . 'Finished!' . PHP_EOL);
        $finished_date = Yii::$app->formatter->asDate('now', 'yyyy-MM-dd h:m:s');
        $this->stdout($finished_date . PHP_EOL);


        // Diff in minutes
        $diff = (strtotime($finished_date) - strtotime($start_date)) / 60;
        $this->stdout(PHP_EOL . 'Duration in minutes: ' . round($diff) . PHP_EOL);
    }

    public function actionUpdateExistingShows($imdb_id = null)
    {

    }
}
