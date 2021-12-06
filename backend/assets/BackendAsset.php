<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 7/3/14
 * Time: 3:14 PM
 */

namespace backend\assets;

use common\assets\AdminLte;
use common\assets\Html5shiv;
use common\assets\Minibarjs;
use common\assets\Axios;
use common\assets\BootstrapToggle;
use common\assets\NotifyJs;
use common\assets\MomentJs;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

class BackendAsset extends AssetBundle
{

    public $sourcePath = '@backend/resources';

    /**
     * @var array
     */
    public $css = [
        'css/style.css',
        'css/css-circular-prog-bar.css'
    ];
    /**
     * @var array
     */
    public $js = [
        'js/app.js',
        'js/movies-featured/search.js'
    ];

    /**
     * @var array
     */
    public $depends = [
        YiiAsset::class,
        AdminLte::class,
        Html5shiv::class,
        Axios::class,
        Minibarjs::class,
        BootstrapToggle::class,
        NotifyJs::class,
        MomentJs::class,
    ];
}
