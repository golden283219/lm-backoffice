<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\ShowsEpisodesSubtitles;
use common\assets\Select2;
/* @var $this yii\web\View */
/* @var $searchModel common\models\ShowsEpisodesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Episodes Moderation';
$this->params['breadcrumbs'][] = $this->title;

$GLOBALS['back_url'] = $currentUrl;
select2::register($this);

?>
<div class="shows-episodes-index">
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'label' => 'TV Show',
                'format' => function ($model) {
                    return $this->render('_e-card', [
                        'model' => $model,
                        'subtitles_count' => ShowsEpisodesSubtitles::find()->where(['id_episode' => $model->id, 'is_moderated' => 1])->count()
                    ]);
                },
                'attribute' => 'id_shows',
                'headerOptions' => ['style' => 'width:30%'],
                'value' => function ($model) {
                    return $model;
                },
            ],
            'season',
            'episode',
            [
                'label' => 'Mode',
                'format' => function ($value) {
                    switch ($value->is_locked) {
                        case '0':
                            $formatted_value = '<span class="badge badge-success">UNLOCKED</span>';
                            break;

                        case '1':
                            $formatted_value = '<span class="badge badge-warning">LOCKED</span>';
                            break;

                        case '2':
                            $formatted_value = '<span class="badge badge-info">DRAFT</span>';
                            break;

                        case '3':
                            $formatted_value = '<span class="badge badge-dark">BEING CONVERTED</span>';
                            break;

                        default:
                            $formatted_value = '(not set)';
                            break;
                    }

                    return $formatted_value;
                },
                'attribute' => 'is_locked',
                'filter' => [
                    0 => 'UNLOCKED',
                    1 => 'LOCKED',
                    2 => 'DRAFT',
                    3 => 'BEING CONVERTED',
                ],
                'value' => function ($model) {
                    return $model;
                },
            ],
            'air_date',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{moderate}',
                'buttons' => [
                    'moderate' => function ($url, $model) {
                        return '<a class="btn btn-block btn-primary btn-sm" title="Edit Episode" href=' . '/moderation/episodes/update?id=' . $model->id . '>Moderate</a>';
                    }
                ],
            ],
        ],
    ]); ?>
</div>

<script>
  window.addEventListener('DOMContentLoaded', function () {
    $('input[name="ShowsEpisodesSearch[id_shows]"]').hide().parent().append('<select id="ShowsMetaIdTvhow"></select>');
    $('#ShowsMetaIdTvhow').select2({
      ajax: {
        url: '<?= env('API_HOST_INFO');?>/shows/search',
        dataType: 'json',
        processResults: function (data) {
          let results = [];
          results.push({
            id: '',
            text: '[Any TV Show]'
          });
          data.forEach(item => {
            results.push({
              id: item.id_show,
              text: item.title + ' (' + item.year.split('-')['0'] + ')'
            });
          });
          return {
            results: results
          };
        }
      }
    }).on('change', function (event) {
      event.stopPropagation();
      $('input[name="ShowsEpisodesSearch[id_shows]"]')
        .val($('#ShowsMetaIdTvhow').val())
        .trigger('change');
    });
  });
</script>

