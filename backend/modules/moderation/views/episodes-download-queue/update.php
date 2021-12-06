<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\queue\ShowsMeta */

$this->title = 'Update Episode: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Shows Meta', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id_meta]];
$this->params['breadcrumbs'][] = 'Update';

\backend\assets\VueBundle::register($this);

?>
<div class="shows-meta-update">
    <div class="shows-meta-form">
        <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <div class="col-sm-12">
                <?php echo $form->errorSummary($model); ?>
            </div>
        </div>

        <div class="row">

            <div class="col-sm-2">
                <?php echo $form->field($model, 'season')->textInput() ?>
            </div>

            <div class="col-sm-2">
                <?php echo $form->field($model, 'episode')->textInput() ?>
            </div>

            <div class="col-sm-4">
                <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-sm-2">
                <?php echo $form->field($model, 'air_date')->textInput() ?>
            </div>

            <div class="col-sm-2">
                <?php echo $form->field($model, 'state')->textInput() ?>
            </div>

        </div>

        <div class="row">
            <div class="col-sm-10"></div>
            <div class="col-sm-2">
                <?php echo $form->field($model, 'priority')->textInput() ?>
            </div>
        </div>

        <div class="row" id="torrent-uploader">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="checkbox pull-right">
                            <label>
                                <input type="checkbox" v-model="forceTorrent">
                                Force Torrent Download
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row" v-if="forceTorrent" style="display: none;"
                     v-bind:style="{ display: !forceTorrent ? 'none' : 'block' }">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Release Title:</label>
                            <input type="text" name="ShowsMeta[rel_title]" class="form-control" placeholder="Enter ..."
                                   v-model="releaseTitleModel">
                        </div>
                        <div class="form-group">
                            <label>Magnet Link / Torrent Content</label>
                            <textarea class="form-control" name="ShowsMeta[torrent_blob]" rows="3"
                                      placeholder="Enter ..." v-model="uploadContentModel"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="form-group" style="text-align: right;">
                    <?php echo Html::a('<i class="fa fa-times-circle-o" aria-hidden="true"></i> Cancel', \Yii::$app->request->getReferrer(), ['class' => 'btn btn-warning']) ?>
                    <?php echo Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>

<script>

    window.addEventListener('DOMContentLoaded', function () {
        window.vueApp = new Vue({
            el: '#torrent-uploader',
            name: 'torrentUploader',
            data() {
                return {
                    forceTorrent: false,
                    priority: 0,
                    releaseTitleModel: '<?= $model->rel_title; ?>',
                    uploadContentModel: '<?= $model->torrent_blob; ?>',
                }
            },
            watch: {
                'forceTorrent': function () {
                    if (this.forceTorrent) {
                        $('#showsmeta-state').val('4');
                        $('#showsmeta-priority').val('99');
                    } else {
                        $('#showsmeta-state').val('<?= $model->state; ?>');
                        $('#showsmeta-priority').val('<?= $model->priority; ?>');
                    }
                }
            },
            methods: {
                onFileChange: function (e) {
                    this.isAjaxing = true;
                    let files = e.target.files || e.dataTransfer.files;
                    let reader = new FileReader();
                    let vm = this;

                    reader.onload = (e) => {
                        vm.uploadContentBin = e.target.result;
                        vm.uploadContentModel = vm.uploadContentBin;
                        if (typeof(files[0].name) !== 'undefined' && files[0].name !== '') {
                            vm.releaseTitleModel = files[0].name;
                        }
                        vm.isAjaxing = false;
                    };
                    reader.readAsDataURL(files[0]);
                }
            },
            created() {
                this.downloadStatus = $('#movies-is_downloaded').val();
                this.flag_quality = $('#movies-flag_quality').val();
                this.priority = $('#movies-priority').val();
            }
        });
    });

</script>
