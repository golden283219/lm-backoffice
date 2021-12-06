<?php

namespace backend\models\queue;

/**
 * Youtube converters model
 */
class YtConverters extends \common\models\queue\YtConverters
{
    public function get_clean_ip ()
    {
        $parts = explode(':', $this->ip);

        return isset ($parts['0']) ? $parts['0'] : null;
    }
}
