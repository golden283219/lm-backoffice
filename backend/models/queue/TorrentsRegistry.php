<?php
/**
 * Created by PhpStorm.
 * User: dev136
 * Date: 26.03.2020
 * Time: 15:31
 */

namespace backend\models\queue;


class TorrentsRegistry extends \common\models\queue\TorrentsRegistry
{
    const STATUS_FRESH = 0;
    const STATUS_FINISHED = 1;
    const STATUS_IN_PROGRESS = 2;

    const TYPE_TORRENT = 0;
    const TYPE_MAGNET = 1;

}