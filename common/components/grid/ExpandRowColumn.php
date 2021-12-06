<?php

namespace common\components\grid;

use common\assets\ExpandRowColumnAsset;
use kartik\grid\GridView;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;


class ExpandRowColumn extends \kartik\grid\ExpandRowColumn
{
    public function init()
    {
        if (!isset($this->detailRowCssClass)) {
            $this->detailRowCssClass = $this->grid->getCssClass(GridView::BS_TABLE_INFO);
        }
        if (!isset($this->msgDetailLoading)) {
            $this->msgDetailLoading = Yii::t('kvgrid', '<small>Loading &hellip;</small>');
        }
        $this->initColumnSettings([
            'hiddenFromExport' => true,
            'mergeHeader' => true,
            'hAlign' => GridView::ALIGN_CENTER,
            'vAlign' => GridView::ALIGN_MIDDLE,
            'width' => '50px',
        ]);
        parent::init();
        if (empty($this->detail) && empty($this->detailUrl)) {
            throw new InvalidConfigException("Either the 'detail' or 'detailUrl' must be entered");
        }
        $this->format = 'raw';
        $this->expandIcon = $this->getIcon('expand');
        $this->collapseIcon = $this->getIcon('collapse');
        $this->setProp('expandTitle', Yii::t('kvgrid', 'Expand'));
        $this->setProp('collapseTitle', Yii::t('kvgrid', 'Collapse'));
        $this->setProp('expandAllTitle', Yii::t('kvgrid', 'Expand All'));
        $this->setProp('collapseAllTitle', Yii::t('kvgrid', 'Collapse All'));
        $onDetailLoaded = $this->onDetailLoaded;
        if (!empty($onDetailLoaded) && !$onDetailLoaded instanceof JsExpression) {
            $onDetailLoaded = new JsExpression($onDetailLoaded);
        }
        if ($this->allowBatchToggle) {
            $this->headerOptions['title'] = $this->expandAllTitle;
        }
        if ($this->allowBatchToggle && $this->defaultHeaderState === GridView::ROW_EXPANDED) {
            $this->headerOptions['title'] = $this->collapseTitle;
        }
        $class = 'kv-expand-header-cell';
        $class .= $this->allowBatchToggle ? ' kv-batch-toggle' : ' text-muted';
        Html::addCssClass($this->headerOptions, $class);
        $view = $this->grid->getView();
        ExpandRowColumnAsset::register($view);
        $clientOptions = Json::encode(
            [
                'gridId' => $this->grid->options['id'],
                'hiddenFromExport' => $this->hiddenFromExport,
                'detailUrl' => empty($this->detailUrl) ? '' : $this->detailUrl,
                'onDetailLoaded' => $onDetailLoaded,
                'expandIcon' => $this->expandIcon,
                'collapseIcon' => $this->collapseIcon,
                'expandTitle' => $this->expandTitle,
                'collapseTitle' => $this->collapseTitle,
                'expandAllTitle' => $this->expandAllTitle,
                'collapseAllTitle' => $this->collapseAllTitle,
                'rowCssClass' => $this->detailRowCssClass,
                'animationDuration' => $this->detailAnimationDuration,
                'expandOneOnly' => $this->expandOneOnly,
                'enableRowClick' => $this->enableRowClick,
                'enableCache' => $this->enableCache,
                'rowClickExcludedTags' => array_map('strtoupper', $this->rowClickExcludedTags),
                'collapseAll' => false,
                'expandAll' => false,
                'extraData' => $this->extraData,
                'msgDetailLoading' => $this->msgDetailLoading
            ]
        );
        $this->_hashVar = 'kvExpandRow_' . hash('crc32', $clientOptions);
        $this->_colId = $this->grid->options['id'] . '_' . $this->columnKey;
        Html::addCssClass($this->contentOptions, $this->_colId);
        Html::addCssClass($this->headerOptions, $this->_colId);
        $view->registerJs("var {$this->_hashVar} = {$clientOptions};\n", View::POS_HEAD);
        $view->registerJs("kvExpandRow({$this->_hashVar}, '{$this->_colId}');");
    }
}
