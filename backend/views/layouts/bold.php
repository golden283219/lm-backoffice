<?php

use yii\helpers\Html;

?>

<!DOCTYPE html>
<html lang="<?php echo Yii::$app->language ?>">
<head>
	<meta charset="UTF-8">
	<meta charset="<?php echo Yii::$app->charset ?>">
	<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

	<?php echo Html::csrfMetaTags() ?>
	<title><?php echo Html::encode($this->title) ?></title>
	<style>
		iframe {
			width: 100%;
			height: 100%;
			position: absolute;
			left: 0;
			top: 0;
		}
	</style>
</head>
<?php $this->beginBody() ?>
<?php echo $content ?>
<?php $this->endBody() ?>
</html>
