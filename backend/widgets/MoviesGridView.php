<?php


namespace backend\widgets;


use yii\helpers\Html;
use yii\grid\GridView;

class MoviesGridView extends GridView
{
    public function renderEmpty()
    {
        return Html::tag('span', 'Nothing found. ') .
            Html::a(' <i class="fa fa-plus-circle" aria-hidden="true"></i> Add [' . $this->filterModel->imdb_id . ']', '/moderation/movies-download-queue/create?imdb_id=' . $this->filterModel->imdb_id);
    }
}
