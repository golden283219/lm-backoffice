<?php

echo $this->renderAjax('_form', [
    'model' => $model,
    'enableAjaxValidation' => true
]);
