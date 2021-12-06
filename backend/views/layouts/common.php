<?php

use backend\assets\BackendAsset;
use backend\modules\system\models\SystemLog;
use common\models\site\MoviesModeration;
use backend\modules\moderation\models\ModerationDraft;
use backend\widgets\Menu;
use common\models\site\ShowsEpisodesReportsCache;
use yii\bootstrap\Alert;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\log\Logger;
use yii\widgets\Breadcrumbs;

$bundle = BackendAsset::register($this);
?>

<?php $this->beginContent('@backend/views/layouts/base.php'); ?>

<div class="wrapper">
    <!-- header logo: style can be found in header.less -->
    <header class="main-header">
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <div class="navbar-custom-menu float-left">
                <ul class="nav navbar-nav">
                    <li>
                        <?php echo Html::a(
                            '<i class="fa fa-envelope-o" aria-hidden="true"></i> Compose Email',
                            ['/application/email/compose'],
                            ['id' => 'compose-email-global']
                        ) ?>
                    </li>
                </ul>
            </div>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- Notifications: style can be found in dropdown.less -->
                    <li id="log-dropdown" class="dropdown notifications-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-warning"></i>
                            <span class="label label-danger">
                                <?php echo SystemLog::find()->count() ?>
                            </span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">
                                <?php echo Yii::t('backend', 'You have {num} log items', ['num' => SystemLog::find()->count()]) ?>
                            </li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu">
                                    <?php foreach (SystemLog::find()->orderBy(['log_time' => SORT_DESC])->limit(5)->all() as $logEntry): ?>
                                        <li>
                                            <a href="<?php echo Yii::$app->urlManager->createUrl(['/system/log/view', 'id' => $logEntry->id]) ?>">
                                                <i class="fa fa-warning <?php echo $logEntry->level === Logger::LEVEL_ERROR ? 'text-red' : 'text-yellow' ?>"></i>
                                                <?php echo $logEntry->category ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                            <li class="footer">
                                <?php echo Html::a(Yii::t('backend', 'View all'), ['/system/log/index']) ?>
                            </li>
                        </ul>
                    </li>
                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img
                                    src="<?php echo Yii::$app->user->identity->userProfile->getAvatar($this->assetManager->getAssetUrl($bundle, 'img/anonymous.jpg')) ?>"
                                    class="user-image">
                            <span><?php echo Yii::$app->user->identity->username ?> <i class="caret"></i></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header light-blue">
                                <img
                                        src="<?php echo Yii::$app->user->identity->userProfile->getAvatar($this->assetManager->getAssetUrl($bundle, 'img/anonymous.jpg')) ?>"
                                        class="img-circle" alt="User Image"/>
                                <p>
                                    <?php echo Yii::$app->user->identity->username ?>
                                    <small>
                                        <?php echo Yii::t('backend', 'Member since {0, date, short}', Yii::$app->user->identity->created_at) ?>
                                    </small>
                                </p>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <?php echo Html::a(Yii::t('backend', 'Profile'), ['/sign-in/profile'], ['class' => 'btn btn-default btn-flat']) ?>
                                </div>
                                <div class="pull-left">
                                    <?php echo Html::a(Yii::t('backend', 'Account'), ['/sign-in/account'], ['class' => 'btn btn-default btn-flat']) ?>
                                </div>
                                <div class="pull-right">
                                    <?php echo Html::a(Yii::t('backend', 'Logout'), ['/sign-in/logout'], ['class' => 'btn btn-default btn-flat', 'data-method' => 'post']) ?>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <?php echo Html::a('<i class="fa fa-cogs"></i>', ['/system/settings']) ?>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <div class="main-sidebar--overlay"></div>
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="<?php echo Yii::$app->user->identity->userProfile->getAvatar($this->assetManager->getAssetUrl($bundle, 'img/anonymous.jpg')) ?>" class="img-circle"/>
                </div>
                <div class="pull-left info">
                    <p><?php echo Yii::t('backend', 'Hello, {username}', ['username' => Yii::$app->user->identity->getPublicIdentity()]) ?></p>
                    <a href="<?php echo Url::to(['/sign-in/profile']) ?>">
                        <i class="fa fa-circle text-success"></i>
                        <?php echo date('d F, Y') . ' <span class="spacer-5"></span> ' . date('H:i:s'); ?>
                    </a>
                </div>
            </div>

            <!-- sidebar menu: : style can be found in sidebar.less -->
            <?php echo Menu::widget([
                'options' => ['class' => 'sidebar-menu tree', 'data' => ['widget' => 'tree']],
                'linkTemplate' => '<a href="{url}">{icon}<span>{label}</span>{right-icon}{badge}</a>',
                'submenuTemplate' => "\n<ul class=\"treeview-menu\">\n{items}\n</ul>\n",
                'activateParents' => true,

                // Moderation
                'items' => [
                    [
                        'label' => Yii::t('backend', ''),
                        'options' => ['class' => 'header'],
                        'visible' => Yii::$app->user->can('super_moderator')
                    ],
                    [
                        'label' => Yii::t('backend', 'Drafts'),
                        'icon' => '<i class="fa fa-certificate"></i>',
                        'url' => ['/moderation/draft'],
                        'active' => Yii::$app->controller->id === 'draft',
                        'visible' => Yii::$app->user->can('super_moderator'),
                        'badge' => ModerationDraft::find()->where(['status' => 0, 'is_active' => 1])->count(),
                        'badgeBgClass' => 'label-primary'
                    ],
                    [
                        'label' => Yii::t('backend', ''),
                        'options' => ['class' => 'header'],
                        'visible' => Yii::$app->user->can('super_moderator')
                    ],

                    // Movies Moderation
                    [
                        'label' => Yii::t('backend', 'Movies'),
                        'url' => '#',
                        'icon' => '<i class="fa fa-video-camera" aria-hidden="true"></i>',
                        'options' => ['class' => 'treeview'],
                        'active' => in_array(Yii::$app->controller->id, [
                            'movies',
                            'movies-reports',
                            'movies-download-queue',
                            'movies-moderation-history',
                            'movies-featured'
                        ]),
                        'visible' => Yii::$app->user->can('moderator'),
                        'items' => [
                            [
                                'label' => 'Site Movies',
                                'icon' => '<i class="fa fa-film"></i>',
                                'url' => ['/moderation/movies'],
                                'active' => Yii::$app->controller->id === 'movies',
                                'visible' => Yii::$app->user->can('moderator'),
                            ],
                            [
                                'label' => 'Featured Movies',
                                'icon' => '<i class="fa fa-first-order" aria-hidden="true"></i>',
                                'url' => ['/moderation/movies-featured'],
                                'active' => Yii::$app->controller->id === 'movies-featured',
                                'visible' => Yii::$app->user->can('super_moderator'),
                            ],
                            [
                                'label' => Yii::t('backend', 'Reports'),
                                'icon' => '<i class="fa fa-bug" aria-hidden="true"></i>',
                                'url' => ['/moderation/movies-reports'],
                                'active' => Yii::$app->controller->id === 'movies-reports',
                                'badge' => MoviesModeration::find()->where('active_reports_count > 0')->count(),
                                'badgeBgClass' => 'label-danger',
                                'visible' => Yii::$app->user->can('moderator'),
                            ],
                            [
                                'label' => Yii::t('backend', 'Queue'),
                                'icon' => '<i class="fa fa-list-ul"></i>',
                                'url' => ['/moderation/movies-download-queue'],
                                'active' => Yii::$app->controller->id === 'movies-download-queue',
                                'visible' => Yii::$app->user->can('moderator'),
                            ],
                            [
                                'label' => Yii::t('backend', 'History'),
                                'icon' => '<i class="fa fa-history"></i>',
                                'url' => ['/moderation/movies-moderation-history'],
                                'active' => Yii::$app->controller->id === 'movies-moderation-history',
                                'visible' => Yii::$app->user->can('moderator'),
                            ],
                        ]
                    ],



                    // TV Shows Moderation
                    [
                        'label' => Yii::t('backend', 'TV Shows'),
                        'url' => '#',
                        'icon' => '<i class="fa fa-television" aria-hidden="true"></i>',
                        'options' => ['class' => 'treeview'],
                        'active' => in_array(Yii::$app->controller->id, [
                            'episodes',
                            'episodes-reports',
                            'episodes-download-queue',
                            'shows-download-queue',
                            'episodes-moderation-history'
                        ]),
                        'visible' => Yii::$app->user->can('moderator'),
                        'items' => [
                            [
                                'label' => Yii::t('backend', 'Site Episodes'),
                                'icon' => '<i class="fa fa-list-ul"></i>',
                                'url' => ['/moderation/episodes'],
                                'active' => Yii::$app->controller->id === 'episodes',
                                'visible' => Yii::$app->user->can('moderator'),
                            ],
                            [
                                'label' => Yii::t('backend', 'Reports'),
                                'icon' => '<i class="fa fa-bug"></i>',
                                'url' => ['/moderation/episodes-reports'],
                                'active' => Yii::$app->controller->id === 'episodes-reports',
                                'badge' => ShowsEpisodesReportsCache::find()->where(['is_closed' => 0])->count(),
                                'badgeBgClass' => 'label-danger',
                                'visible' => Yii::$app->user->can('moderator'),
                            ],
                            [
                                'label' => '',
                                'options' => ['class' => 'header'],
                                'visible' => true
                            ],
                            [
                                'label' => 'Add Show',
                                'icon' => '<i class="fa fa-circle-o"></i>',
                                'url' => ['/moderation/shows-download-queue/add'],
                                'active' => Yii::$app->controller->id === 'shows-download-queue' && Yii::$app->controller->action->id === 'add',
                                'visible' => Yii::$app->user->can('moderator'),
                            ],
                            [
                                'label' => Yii::t('backend', 'Episodes Queue'),
                                'icon' => '<i class="fa fa-circle-o"></i>',
                                'url' => ['/moderation/episodes-download-queue'],
                                'active' => Yii::$app->controller->id === 'episodes-download-queue',
                                'visible' => Yii::$app->user->can('moderator'),
                            ],
                            [
                                'label' => Yii::t('backend', 'Shows Queue'),
                                'icon' => '<i class="fa fa-circle-o"></i>',
                                'url' => ['/moderation/shows-download-queue'],
                                'active' => Yii::$app->controller->id === 'shows-download-queue' & Yii::$app->controller->action->id === 'index',
                                'visible' => Yii::$app->user->can('administrator'),
                            ],
                            [
                                'label' => Yii::t('backend', 'History'),
                                'icon' => '<i class="fa fa-history"></i>',
                                'url' => ['/moderation/episodes-moderation-history'],
                                'active' => Yii::$app->controller->id === 'episodes-moderation-history',
                                'visible' => Yii::$app->user->can('moderator'),
                            ],
                        ]
                    ],


                    /**
                     * Contents
                     */
                    [
                        'label' => Yii::t('backend', 'Contents'),
                        'url' => '#',
                        'icon' => '<i class="fa fa-columns" aria-hidden="true"></i>',
                        'options' => ['class' => 'treeview'],
                        'active' => in_array(Yii::$app->controller->id, [
                            'default'
                        ]),
                        'visible' => Yii::$app->user->can('administrator'),
                        'items' => [
                            [
                                'label' => Yii::t('backend', 'Home Page'),
                                'icon' => '<i class="fa fa-file-text-o" aria-hidden="true"></i>',
                                'url' => ['/HomePage/'],
                                'active' => in_array(Yii::$app->controller->action->uniqueId, [
                                    'HomePage/default/index',
                                    'HomePage/default/view',
                                    'HomePage/default/update',
                                    'HomePage/default/create'
                                ]),
                                'visible' => Yii::$app->user->can('administrator'),
                            ],
                            [
                                'label' => Yii::t('backend', 'Static Pages'),
                                'icon' => '<i class="fa fa-file-text-o" aria-hidden="true"></i>',
                                'url' => ['/StaticPages/'],
                                'active' => in_array(Yii::$app->controller->action->uniqueId, ['StaticPages/default/index', 'StaticPages/default/view', 'StaticPages/default/create']),
                                'visible' => Yii::$app->user->can('administrator'),
                            ],
                            [
                                'label' => Yii::t('backend', 'Notifications'),
                                'url' => ['/system/global-messages/index'],
                                'icon' => '<i class="fa fa-sticky-note"></i>',
                                'active' => (Yii::$app->controller->id == 'global-messages'),
                                'visible' => Yii::$app->user->can('administrator')
                            ],
                        ],
                    ],

                    /**
                     * Premium Users Section
                     */
                    [
                        'label' => Yii::t('backend', 'Premium'),
                        'url' => '#',
                        'icon' => '<i class="fa fa-paypal" aria-hidden="true"></i>',
                        'options' => ['class' => 'treeview'],
                        'active' => Yii::$app->controller->module->id === 'premium',
                        'visible' => Yii::$app->user->can('administrator'),
                        'items' => [
                            [
                                'label' => Yii::t('backend', 'Members'),
                                'icon' => '<i class="fa fa-user-circle-o" aria-hidden="true"></i>',
                                'url' => ['/premium/members'],
                                'active' => Yii::$app->controller->id === 'members' && Yii::$app->controller->module->id === 'premium',
                                'visible' => Yii::$app->user->can('administrator'),
                            ],
                            [
                                'label' => Yii::t('backend', 'Plans'),
                                'url' => ['/premium/plans'],
                                'icon' => '<i class="fa fa-shopping-basket" aria-hidden="true"></i>',
                                'active' => Yii::$app->controller->id === 'plans' && Yii::$app->controller->module->id === 'premium',
                                'visible' => Yii::$app->user->can('administrator')
                            ],
                        ],
                    ],

                    /**
                     * Youtube Converters
                     */
                    [
                        'label' => Yii::t('backend', 'YouTube Converters'),
                        'url' => '/youtube/servers/index',
                        'icon' => '<i class="fa fa-youtube" aria-hidden="true"></i>',
                        'active' => in_array(Yii::$app->controller->id, ['servers']),
                        'visible' => Yii::$app->user->can('administrator'),
                    ],


                    /**
                     * System
                     */

                    [
                        'label' => Yii::t('backend', 'System'),
                        'url' => '#',
                        'icon' => '<i class="fa fa-cogs"></i>',
                        'options' => ['class' => 'treeview'],
                        'active' => in_array(Yii::$app->controller->id, [
                            'global-messages',
							'collection',
                            'key-storage',
                            'fe-servers',
                            'settings',
                            'log'
                        ]),
                        'items' => [
                            [
                                'label' => Yii::t('backend', 'Key-Value Storage'),
                                'url' => ['/system/key-storage/index'],
                                'icon' => '<i class="fa fa-arrows-h"></i>',
                                'active' => (Yii::$app->controller->id == 'key-storage'),
                                'visible' => Yii::$app->user->can('administrator')
                            ],
                            [
                                'label' => Yii::t('backend', 'Global Messages'),
                                'url' => ['/system/global-messages/index'],
                                'icon' => '<i class="fa fa-list-alt"></i>',
                                'active' => (Yii::$app->controller->id == 'global-messages'),
                                'visible' => Yii::$app->user->can('administrator')
                            ],
                            [
                                'label' => Yii::t('backend', 'Collections'),
                                'url' => ['/system/collection/index'],
                                'icon' => '<i class="fa fa-list"></i>',
                                'active' => (yii::$app->controller->id == 'collection'),
                                'visible' => Yii::$app->user->can('administrator')
                            ],
                            [
                                'label' => Yii::t('backend', 'Settings'),
                                'url' => ['/system/settings'],
                                'icon' => '<i class="fa fa-cogs"></i>',
                                'active' => (Yii::$app->controller->id == 'settings'),
                                'visible' => Yii::$app->user->can('administrator')
                            ],
                            [
                                'label' => Yii::t('backend', 'Streaming Servers'),
                                'url' => ['/system/fe-servers'],
                                'icon' => '<i class="fa fa-server" aria-hidden="true"></i>',
                                'active' => (Yii::$app->controller->id == 'fe-servers'),
                                'visible' => Yii::$app->user->can('administrator')
                            ],
                            [
                                'label' => Yii::t('backend', 'Users'),
                                'icon' => '<i class="fa fa-users"></i>',
                                'url' => ['/user/index'],
                                'active' => Yii::$app->controller->id === 'user',
                                'visible' => Yii::$app->user->can('administrator'),
                            ],
                            [
                                'label' => Yii::t('backend', 'User Actions'),
                                'icon' => '<i class="fa fa-user-secret" aria-hidden="true"></i>',
                                'url' => ['/system/users-log'],
                                'active' => Yii::$app->controller->id === 'users-log',
                                'visible' => Yii::$app->user->can('administrator'),
                            ],
                            [
                                'label' => Yii::t('backend', 'System Logs'),
                                'url' => ['/system/log/index'],
                                'icon' => '<i class="fa fa-warning"></i>',
                                'badge' => SystemLog::find()->count(),
                                'badgeBgClass' => 'label-danger',
                                'visible' => Yii::$app->user->can('administrator')
                            ],
                        ],
                        'visible' => Yii::$app->user->can('administrator'),
                    ]
                ],
            ]) ?>
        </section>
        <!-- /.sidebar -->
    </aside>
    <!-- Right side column. Contains the navbar and content of the page -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <?php echo $this->title ?>
                <?php if (isset($this->params['subtitle'])): ?>
                    <small><?php echo $this->params['subtitle'] ?></small>
                <?php endif; ?>
            </h1>

            <?php echo Breadcrumbs::widget([
                'tag' => 'ol',
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php if (Yii::$app->session->hasFlash('alert')): ?>
                <?php echo Alert::widget([
                    'body' => ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'body'),
                    'options' => ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'options'),
                ]) ?>
            <?php endif; ?>
            <?php echo $content ?>
        </section>
    </div>

    <footer class="main-footer">
        <strong>&copy; LookMovie.com <?php echo date('Y') ?></strong>
        <div class="pull-right">ver. <?= env('APP_VERSION'); ?></div>
    </footer>
</div>

<?php $this->endContent(); ?>
