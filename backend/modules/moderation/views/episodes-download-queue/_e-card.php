<?php
  $date = isset($model->show->first_air_date) && isset(explode('-', $model->show->first_air_date)['0']) ? explode('-', $model->show->first_air_date)['0'] : '0000';
?>
<div class="m-card">
	<div class="m-card__meta">
		<span class="m-card__title"><?= isset($model->show->title) ? $model->show->title : ''; ?> (<?= $date; ?>) - <?= isset($model->show->imdb_id) ? $model->show->imdb_id : '';?></span>
	</div>
</div>
