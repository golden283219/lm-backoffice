<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\moderation\models\MoviesDownloadQueue */
/* @var $form yii\bootstrap\ActiveForm */

\backend\assets\VueBundle::register($this);

?>

<div class="movies-download-queue-form">

  <?php $form = ActiveForm::begin(); ?>

  <div class="row">
    <div class="col-sm-12">
      <?php echo $form->errorSummary($model); ?>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-5">
      <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-sm-2">
      <?php echo $form->field($model, 'year')->textInput() ?>
    </div>
    <div class="col-sm-3">
      <?php echo $form->field($model, 'imdb_id')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-sm-2">
      <?php echo $form->field($model, 'is_downloaded')->dropDownList([
        '1' => 'Downloaded',
        '2'=>'Converting',
        '3'=>'Picked By Worker',
        '10' => 'Waiting Usenet',
        '13' => 'Waiting Torrent',
        '14' => 'Missing Download'
      ]); ?>
    </div>
  </div>

  <div class="row" id="torrent-uploader">
    <div class="col-sm-10">
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
      <div class="row" v-if="forceTorrent" style="display: none;" v-bind:style="{ display: !forceTorrent ? 'none' : 'block' }">
        <div class="col-sm-12">
          <div class="form-group">
            <label>Release Title:</label>
            <input type="text" name="Movies[rel_title]" class="form-control" placeholder="Enter ..." v-model="releaseTitleModel">
          </div>
          <div class="form-group">
            <label>Magnet Link / Torrent Content</label>
            <textarea class="form-control" name="Movies[torrent_blob]" rows="3" placeholder="Enter ..." v-model="uploadContentModel"></textarea>
          </div>
          <div class="form-group">
            <label for="torrentFileUpload">Torrent File Upload</label>
            <input type="file" id="torrentFileUpload" @change="onFileChange">
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-2">

      <div class="row">
        <div class="col-sm-12">
          <?php echo $form->field($model, 'flag_quality')->textInput() ?>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-12">
          <?php echo $form->field($model, 'original_language')->textInput(['maxlength' => true]) ?>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-12">
          <?php echo $form->field($model, 'priority')->textInput() ?>
        </div>
      </div>

    </div>
  </div>

  <div class="form-group">
    <?php echo Html::submitButton('Update', ['class' => 'btn btn-primary pull-right']) ?>
  </div>

  <?php ActiveForm::end(); ?>

</div>


<script>

  window.addEventListener('DOMContentLoaded', function () {
    window.vueApp = new Vue({
      el: '#torrent-uploader',
      name: 'torrentUploader',
      data () {
        return {
          forceTorrent: false,
          flag_quality: '',
          downloadStatus: 1,
          priority: 0,
          releaseTitleModel: '',
          uploadContentModel: '',
        }
      },
      watch: {
        'forceTorrent': function () {
          if (this.forceTorrent) {
            $('#movies-is_downloaded').val('13');
            $('#movies-flag_quality').val('0');
            $('#movies-priority').val('6');
          } else {
            $('#movies-is_downloaded').val(this.downloadStatus);
            $('#movies-flag_quality').val(this.flag_quality);
            $('#movies-priority').val(this.priority);
          }
        }
      },
      methods: {
        onFileChange: function(e) {
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
      created () {
        this.downloadStatus = $('#movies-is_downloaded').val();
        this.flag_quality = $('#movies-flag_quality').val();
        this.priority = $('#movies-priority').val();
      }
    });
  });

</script>
