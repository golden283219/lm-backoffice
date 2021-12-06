<?php

namespace common\controllers;

use common\models\queue\GarbageCollection;
use common\models\ShowsEpisodes;
use common\models\ShowsEpisodesSubtitles;
use backend\models\queue\ShowsMeta;
use yii\db\Exception;

class EpisodesController
{

    public function actionReconvert($id_episode, $priority, $silent)
    {

        $SiteEpisode = ShowsEpisodes::find()
            ->where(['id' => $id_episode])
            ->one();

        if ($SiteEpisode) {

            $episode_meta = ShowsMeta::find()
                ->where([
                    'id_tvshow' => $SiteEpisode->id_shows,
                    'episode' => $SiteEpisode->episode,
                    'season' => $SiteEpisode->season
                ])
                ->one();

            if ((int)$silent === 0) {
                foreach ($SiteEpisode->storage as $storage_item) {
                    $this->putInGarbageCollection('/' . $SiteEpisode->shard . '/', $storage_item);
                    break;
                }

                $EpisodeSubtitles = ShowsEpisodesSubtitles::find()->where([
                    'id_episode' => $id_episode
                ])->all();

                foreach ($EpisodeSubtitles as $subtitle) {
                    $subtitle->delete();
                }

                $SiteEpisode->delete();
            }

            $bad_titles = unserialize($episode_meta->bad_titles);
            $bad_titles[] = md5($SiteEpisode->rel_title);
            $episode_meta->bad_titles = serialize($bad_titles);

            $episode_meta->state = 0;
            $episode_meta->priority = (int)$priority;

            $episode_meta->save();

            return [
                'site' => $SiteEpisode ? true : false,
                'queue' => $episode_meta ? true : false
            ];

        }

        return [
            'site' => false,
            'queue' => false
        ];

    }

    private function putInGarbageCollection($storage, $path)
    {

        $episode_path = $this->sanitizeEpisodePath($path);

        if ($episode_path) {
            $gc = new GarbageCollection();
            $gc->storage = $storage;
            $gc->path = $episode_path;
            if ($gc->save()) {
                return true;
            }
        }

        return false;

    }

    private function sanitizeEpisodePath($path)
    {
        $parts = explode('/', $path);

        if (count($parts) === 5) {
            return $parts[0] . '/' . $parts[1] . '/' . $parts[2];
        }

        return false;

    }

}