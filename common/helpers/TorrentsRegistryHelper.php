<?php

namespace common\helpers;

use backend\models\queue\ShowsMeta;
use backend\models\queue\ShowsMetaTorrentMap;
use backend\models\queue\TorrentsRegistry;
use Yii;
use yii\helpers\ArrayHelper;

class TorrentsRegistryHelper
{
    /**
     * Priority For Episodes
     *
     * @var int
     */
    private $priority = 0;

    /**
     * @var array
     */
    private $torrent_registry_cache = [];


    /**
     * TorrentsRegistryHelper constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        foreach ($options as $key => $option) {
            if (property_exists($this, $key)) {
                $this->{$key} = $option;
            }
        }
    }

    public function insert($magnet, $id_meta, $map = null, $rel_title = null, $flag_quality = null)
    {
        $magnet_id = extract_magnet_id($magnet);
        $torrent_registry = $this->GetOrCreateTorrentRegistry($magnet_id, $magnet);

        $this->insertShowsMetaTorrentMap($id_meta, $torrent_registry->id);

        $magnet_with_id = $magnet . '&lmb_id=' . $torrent_registry->id;

        $this->insertShowsMetaTorrentMap($id_meta, $torrent_registry->id);
        $this->updateEpisodeMagnet($id_meta, $magnet_with_id, [
            'map' => $map,
            'rel_title' => $rel_title,
            'flag_quality' => $flag_quality
        ]);
    }

    /**
     * @param $id_meta
     * @param $id_torrent_registry
     *
     * @return bool
     */
    private function insertShowsMetaTorrentMap($id_meta, $id_torrent_registry)
    {
        ShowsMetaTorrentMap::deleteAll('id_meta = ' . $id_meta . ' AND status IN (0,2)');

        $ShowsMetaTorrentMap = new ShowsMetaTorrentMap();
        $ShowsMetaTorrentMap->id_meta = $id_meta;
        $ShowsMetaTorrentMap->status = ShowsMetaTorrentMap::STATUS_FRESH;
        $ShowsMetaTorrentMap->id_torrents_registry = $id_torrent_registry;

        return $ShowsMetaTorrentMap->save();
    }

    /**
     * @param $id_meta
     * @param $magnet
     *
     * @param $options array
     *
     * @return bool
     * @throws \Exception
     */
    private function updateEpisodeMagnet($id_meta, $magnet, $options = [])
    {
        $shows_meta = ShowsMeta::find()->where(['id_meta' => $id_meta])->one();

        if (empty($shows_meta)) {
            return false;
        }

        $map = ArrayHelper::getValue($options, 'map');
        $rel_title = ArrayHelper::getValue($options, 'rel_title');
        $flag_quality = ArrayHelper::getValue($options, 'flag_quality');

        $shows_meta->type = ShowsMeta::TYPE_MAPPED_MAGNET;
        $shows_meta->rel_title = extractMagnetDN($magnet);
        $shows_meta->state = env('EPISODES_QUEUE_WAITING_TORRENT', 4);
        $shows_meta->torrent_blob = $magnet;

        if (!empty($rel_title)) {
            $shows_meta->rel_title = $rel_title;
        }

        if (!empty($map)) {
            $shows_meta->map = $map;
        }

        if (!empty($flag_quality)) {
            $shows_meta->flag_quality = $flag_quality;
        }

        $shows_meta->priority = $this->priority;

        if ($shows_meta->save()) {
            return true;
        }

        Yii::error(json_encode($shows_meta->errors, JSON_PRETTY_PRINT), 'TorrentsRegistryHelper:save()');

        return false;
    }

    /**
     * @param $magnet_id
     *
     * @param $contents
     *
     * @return mixed
     */
    private function GetOrCreateTorrentRegistry($magnet_id, $contents)
    {
        if (!empty($this->torrent_registry_cache[$magnet_id])) {
            return $this->torrent_registry_cache[$magnet_id];
        }

        $torrent_registry = TorrentsRegistry::find()
            ->where(['id_torrent' => $magnet_id])
            ->andWhere(['status' => [TorrentsRegistry::STATUS_FRESH, TorrentsRegistry::STATUS_IN_PROGRESS]])
            ->one();

        if (!empty($torrent_registry)) {
            $this->torrent_registry_cache[$magnet_id] = $torrent_registry;
            return $torrent_registry;
        }

        $torrent_registry = new TorrentsRegistry();
        $torrent_registry->torrent_contents = $contents;
        $torrent_registry->type = TorrentsRegistry::TYPE_MAGNET;
        $torrent_registry->id_torrent = $magnet_id;
        $torrent_registry->save();

        $this->torrent_registry_cache[$magnet_id] = $torrent_registry;

        return $torrent_registry;
    }
}
