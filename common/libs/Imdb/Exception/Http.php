<?php

namespace common\libs\Imdb\Exception;

use common\libs\Imdb\Exception;

class Http extends Exception
{
    public $HTTPStatusCode = null;
}
