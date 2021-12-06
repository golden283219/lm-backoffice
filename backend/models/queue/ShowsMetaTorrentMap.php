<?php

namespace backend\models\queue;


use yii\helpers\ArrayHelper;

class ShowsMetaTorrentMap extends \common\models\queue\ShowsMetaTorrentMap
{
	const STATUS_FRESH = 0;
	const STATUS_FINISHED = 1;
	const STATUS_IN_PROGRESS = 2;

    /**
     * @param $event
     *
     * @throws \Exception
     */
	public static function handle_multi_torrent_logs($event)
    {
        /**
         * @var ShowsMeta
         */
        $shows_meta = $event->data;

        // new values
        $dirty_attr = $shows_meta->getDirtyAttributes();
        // old values
        $old_attr = $shows_meta->getOldAttributes();

        $dirty_state = ArrayHelper::getValue($dirty_attr, 'state');
        $dirty_type = ArrayHelper::getValue($dirty_attr, 'type');

        if ($dirty_state == 0 || $dirty_state == 1 || $dirty_state == 5 || ($dirty_state == 4 && $dirty_type != 2)) {
            self::setEpisodeDone($shows_meta->id_meta);
        }
    }

    /**
     * Set Episode Down in `shows_meta_torrent_map` table
     *
     * @param $id_meta
     */
    public static function setEpisodeDone($id_meta)
    {
        /**
         * @var
         */
        $shows_meta_torrent_maps = self::find()->where(['id_meta' => $id_meta, 'status' => [self::STATUS_FRESH, self::STATUS_IN_PROGRESS]])->all();

        foreach ($shows_meta_torrent_maps as $shows_meta_torrent_map) {
            if (!empty($shows_meta_torrent_map)) {
                $shows_meta_torrent_map->status = self::STATUS_FINISHED;
                $shows_meta_torrent_map->save();
            }
        }
    }
}
