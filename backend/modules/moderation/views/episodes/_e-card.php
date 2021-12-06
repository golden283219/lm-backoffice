<?php
$badge = 'danger';

if ($subtitles_count > 0) {
    $badge = 'warning';
}

if ($subtitles_count > 5) {
    $badge = 'success';
}
?>

<div class="m-card">
	<div class="m-card__img-wrapper">
		<img src="<?= \Yii::$app->imageStorage::poster('w300', $model->show->poster); ?>" alt="<?= $model->show->title; ?>">
	</div>
	<div class="m-card__meta">
		<span class="m-card__title"><?= $model->show->title; ?> (<?= $model->show->year;?>) - <a href="https://imdb.com/title/<?= $model->show->imdb_id;?>" target="_blank"><?= $model->show->imdb_id?></a></span>
    <span class="m-card__subs"><span class="badge badge-<?= $badge; ?>"><?= $subtitles_count; ?></span></span>
  </div>
</div>
