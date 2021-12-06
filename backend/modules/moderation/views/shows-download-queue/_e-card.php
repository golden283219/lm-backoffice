<?php
  $date = isset(explode('-', $model->show->first_air_date)['0']) ? explode('-', $model->show->first_air_date)['0'] : '0000';
?>
<div class="m-card">
	<div class="m-card__meta">
		<span class="m-card__title"><?= $model->show->title; ?> (<?= $date; ?>) - <?= $model->show->imdb_id?></span>
	</div>
</div>
