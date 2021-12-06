<?php

use backend\assets\BackendAsset;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$bundle = BackendAsset::register($this);

$this->params['body-class'] = $this->params['body-class'] ?? null;
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?php echo Yii::$app->language ?>">
<head>
    <meta charset="<?php echo Yii::$app->charset ?>">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <?php echo Html::csrfMetaTags() ?>
    <title><?php echo Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style>
        .notifyjs-bootstrap-base {
            font-size: 17px;
            background-position: 8px 20px !important;
            padding: 17px 20px 17px 33px !important;
        }

        .faded {
            opacity: 0;
        }
    </style>
    <script>
        window.yii2app = {
            csrf: '<?= \Yii::$app->request->csrfToken; ?>',
            apiBaseURL: '<?= env('API_HOST_INFO'); ?>',
            WEBTORRENT_API: '<?= env('WEBTORRENT_API'); ?>'
        };
    </script>
</head>
<?php echo Html::beginTag('body', [
    'class' => implode(' ', [
        ArrayHelper::getValue($this->params, 'body-class'),
        'skin-black'
    ])
]) ?>
<?php $this->beginBody() ?>
<?php echo $content ?>
<?php $this->endBody() ?>

<?php if ($message = Yii::$app->session->getFlash('success')): ?>
    <script>
        $.notify("<?= $message; ?>", {
            className: 'success',
            arrowSize: 22,
            autoHideDelay: 12000
        });
    </script>
<?php endif; ?>

<?php if ($message = Yii::$app->session->getFlash('error')): ?>
    <script>
        $.notify("<?= $message; ?>", {
            className: 'error',
            arrowSize: 22,
            autoHideDelay: 12000
        });
    </script>
<?php endif; ?>

<?php echo Html::endTag('body') ?>
</html>
<?php $this->endPage() ?>
