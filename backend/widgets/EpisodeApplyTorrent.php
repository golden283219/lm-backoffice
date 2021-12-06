<?php

namespace backend\widgets;

use yii\base\Widget;

/**
 * Class EpisodeApplyTorrent
 *
 * @package common\widgets
 */
class EpisodeApplyTorrent extends Widget
{

    public function init()
    {
        parent::init();
    }

    /**
     * Renders the widget.
     */
    public function run()
    {

        return $this->render('episode-apply-torrent');
    }
}
