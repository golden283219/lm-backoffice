<?php


namespace console\commands;

use console\jobs\metadata\CastImdbMetadataJob;
use yii\queue\amqp\Command;
use common\models\queue\CastImdb;
use Yii;
use yii\helpers\Console;

class CastMetadataDownloadQueueCommand extends Command
{
    public function actionScrapeActor($imdb_id = null)
    {
        // 0000375
        if (!empty($imdb_id)) {
            $imdb_id = str_replace('tt', '', $imdb_id);
            $imdb_id = str_replace('nm', '', $imdb_id);

            $exists = CastImdb::find()
                ->where(['imdb_actor_id' => $imdb_id])
                ->exists();

            if ($exists) {
                Yii::$app->castMetadataDownloadQueue->push(new CastImdbMetadataJob([
                    'castImdbId' => $imdb_id,
                ]));

                return Console::output('Added:');
            }

            return Console::output('Not Added:');
        }

        $addedCount = 0;
        foreach (CastImdb::find()->batch(500) as $castBatch) {
            foreach ($castBatch as $castItem) {
                Yii::$app->castMetadataDownloadQueue->push(new CastImdbMetadataJob([
                    'castImdbId' => $castItem->imdb_actor_id,
                ]));
                $addedCount++;
            }
        }

        return Console::output('Added ' . $addedCount . ':');
    }
}
