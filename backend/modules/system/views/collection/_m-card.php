<div class="m-card">
	<div class="m-card__img-wrapper">
		<img src="<?= \Yii::$app->imageStorage::poster('w300', $model['poster'], '/img/poster_placeholder.png'); ?>" alt="<?= $model['title']; ?>">
	</div>
	<div class="m-card__meta">
		<span class="m-card__title"><?= $model['title']; ?> (<?= $model['year'];?>)</span>
        <div><a href="https://www.imdb.com/title/tt<?= $model['imdb_id']?>" target="_blank">tt<?= $model['imdb_id']?></a></div>
	</div>
</div>
