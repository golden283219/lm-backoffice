<div class="m-card">
	<div class="m-card__img-wrapper" style="flex: 0 0 120px;">
		<img src="<?= \Yii::$app->imageStorage::backdrop('w342', $model->backdrop, '/img/backdrop_placeholder.png'); ?>" alt="<?= $model->title; ?>">
	</div>
	<div class="m-card__meta">
		<span class="m-card__title"><?= $model->title; ?> (<?= $model->year;?>) - <a href="https://www.imdb.com/title/tt<?= $model->imdb_id?>" target="_blank">tt<?= $model->imdb_id?></a></span>
	</div>
</div>
