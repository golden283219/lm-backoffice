<?php


namespace console\jobs\putio;

use GuzzleHttp\Exception\GuzzleException;
use Mozammil\Putio\Putio;
use backend\models\queue\ShowsMetaTorrentMap;
use backend\models\queue\TorrentsRegistry;
use Yii;
use yii\base\BaseObject;
use yii\helpers\Json;
use yii\queue\Queue;

class DeleteFinishedTransfers extends BaseObject implements \yii\queue\JobInterface
{

    /**
     * @param Queue $queue which pushed and is handling the job
     *
     * @return void|mixed result of the job execution
     * @throws GuzzleException
     */
    public function execute($queue)
    {
        $putio = new Putio(env('PUTIO_OAUTH_TOKEN'));

        $finished_transfers = $this->getFinishedTransfers();
        $put_io_transfers = Json::decode($putio->transfers()->list());

        if ($put_io_transfers['status'] !== 'OK') {
            return 0;
        }

        foreach ($finished_transfers as $finished_transfer_id) {
            TorrentsRegistry::updateAll(['status' => TorrentsRegistry::STATUS_FINISHED], 'id = ' . $finished_transfer_id);

            foreach ($put_io_transfers['transfers'] as $transfer) {
                $lmb_id = get_lmb_id_from_magnet($transfer['source']);
                $lmb_id = is_string($lmb_id) ? intval($lmb_id, 10) : $lmb_id;
                if (is_numeric($lmb_id) && $lmb_id === $finished_transfer_id) {
                    try {
                        $putio->files()->delete([$transfer['file_id']]);
                        $putio->transfers()->cancel([$transfer['id']]);

                        // need to call second time in case transfer seeding
                        if ($transfer['status'] === 'SEEDING') {
                            $putio->transfers()->cancel([$transfer['id']]);
                        }
                    } catch (\Exception $e) {
                        Yii::error($e->getMessage(), 'PutIOMaintenance:DeleteFinishedTransfers()');
                    }
                }
            }
        }
    }

    /**
     *
     */
    private function getFinishedTransfers()
    {
        $finished_transfers = [];

        $torrents_registry = TorrentsRegistry::find()
            ->where(['status' => [TorrentsRegistry::STATUS_IN_PROGRESS, TorrentsRegistry::STATUS_FRESH]])
            ->all();


        foreach ($torrents_registry as $torrent_registry) {
            $active_downloads = ShowsMetaTorrentMap::find()
                ->where([
                    'status' => [ShowsMetaTorrentMap::STATUS_FRESH, ShowsMetaTorrentMap::STATUS_IN_PROGRESS],
                    'id_torrents_registry' => $torrent_registry->id
                ])
                ->count();
            $active_downloads = !is_string($active_downloads) ?: intval($active_downloads, 10);

            if ($active_downloads === 0) {
                $finished_transfers[] = $torrent_registry->id;
            }
        }

        return $finished_transfers;
    }
}
