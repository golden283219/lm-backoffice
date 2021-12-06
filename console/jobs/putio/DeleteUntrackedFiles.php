<?php


namespace console\jobs\putio;

use Mozammil\Putio\Putio;
use yii\base\BaseObject;
use yii\helpers\Json;

class DeleteUntrackedFiles extends BaseObject implements \yii\queue\JobInterface
{
    public function execute($queue)
    {
        $putio = new Putio(env('PUTIO_OAUTH_TOKEN'));

        $put_io_transfers = Json::decode($putio->transfers()->list());
        $put_io_files = Json::decode($putio->files()->list());

        if (!$put_io_transfers['status'] === 'OK' || !$put_io_files['status'] === 'OK') {
            return false;
        }

        $put_io_transfers_file_ids = array_map(function ($item) {
            return $item['file_id'];
        }, $put_io_transfers['transfers']);

        $put_io_files_ids = array_map(function ($item) {
            return $item['id'];
        }, $put_io_files['files']);

        $ids_to_delete = [];
        foreach ($put_io_files_ids as $put_io_file_id) {
            if (!in_array($put_io_file_id, $put_io_transfers_file_ids)) {
                $ids_to_delete[] = $put_io_file_id;
            }
        }

        if (count($ids_to_delete) > 0) {
            $putio->files()->delete($ids_to_delete);
        }
    }
}
