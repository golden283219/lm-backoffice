<?php

use backend\models\PremPaymentsHistory;
use common\models\site\PremSignupForm;
use kartik\dynagrid\DynaGrid;
use kartik\grid\GridView;
use yii\bootstrap\ButtonDropdown;
use yii\bootstrap\Modal;
use yii\helpers\Html;

$this->title = 'Premium Members';
$this->params['breadcrumbs'][] = $this->title;

$columns = [
    [
        'class' => '\common\components\grid\ExpandRowColumn',
        'enableRowClick' => true,
        'value' => function ($model, $key, $index) {
            return GridView::ROW_COLLAPSED;
        },
        'expandIcon' => '<i class="fa fa-plus-square-o" aria-hidden="true"></i>',
        'collapseIcon' => '<i class="fa fa-minus-square-o" aria-hidden="true"></i>',
        'detailUrl' => '/premium/members/detail'
    ],
    ['class' => 'kartik\grid\SerialColumn', 'order' => DynaGrid::ORDER_FIX_LEFT],
    'email:email',
    [
        'attribute' => 'cancel_timestamp',
        'label' => 'Membership Status',
        'width' => '230px',
        'value' => function ($model) {
            return $model;
        },
        'format' => function ($model) {
            $value = $model->cancel_timestamp;
            $premium_left = premium_left($value);
            if (!$premium_left) {
                $formatted = '<span class="badge badge-danger">Not Active</span>';
            } else {
                $formatted = strtr('<div><span class="badge badge-success">{{days}}</span> days left</div>', [
                    '{{days}}' => number_format($premium_left / (3600 * 24), 1)
                ]);
            }

            $add_remove_time_buttons = Html::tag(
                'div',
                ButtonDropdown::widget([
                    'label' => '<i class="fa fa-plus-circle" aria-hidden="true"></i>',
                    'encodeLabel' => false,
                    'options' => ['class' => 'btn-primary hide-caret'],
                    'dropdown' => [
                        'encodeLabels' => false,
                        'items' => [
                            [
                                'label' => Html::a('12 month', '#', ['data-pjax' => 0]),
                                'options' => ['class' => 'add-remove-time', 'data-url' => '/premium/members/add-time?id=' . $model->id, 'data-duration' => 31536000]
                            ],
                            [
                                'label' => Html::a('6 month', '#', ['data-pjax' => 0]),
                                'options' => ['class' => 'add-remove-time', 'data-url' => '/premium/members/add-time?id=' . $model->id, 'data-duration' => 15552000]
                            ],
                            [
                                'label' => Html::a('3 month', '#', ['data-pjax' => 0]),
                                'options' => ['class' => 'add-remove-time', 'data-url' => '/premium/members/add-time?id=' . $model->id, 'data-duration' => 7884000]
                            ],
                            [
                                'label' => Html::a('1 month', '#', ['data-pjax' => 0]),
                                'options' => ['class' => 'add-remove-time', 'data-url' => '/premium/members/add-time?id=' . $model->id, 'data-duration' => 2678400]
                            ],
                            [
                                'label' => Html::a('1 week', '#', ['data-pjax' => 0]),
                                'options' => ['class' => 'add-remove-time', 'data-url' => '/premium/members/add-time?id=' . $model->id, 'data-duration' => 604800]
                            ],
                            [
                                'label' => Html::a('1 day', '#', ['data-pjax' => 0]),
                                'options' => ['class' => 'add-remove-time', 'data-url' => '/premium/members/add-time?id=' . $model->id, 'data-duration' => 86400]
                            ],
                            [
                                'label' => Html::a('1 hour', '#', ['data-pjax' => 0]),
                                'options' => ['class' => 'add-remove-time', 'data-url' => '/premium/members/add-time?id=' . $model->id, 'data-duration' => 3600]
                            ],
                        ],
                    ],
                ]) .
                ButtonDropdown::widget([
                    'label' => '<i class="fa fa-minus-circle" aria-hidden="true"></i>',
                    'encodeLabel' => false,
                    'options' => ['class' => 'btn-warning hide-caret'],
                    'dropdown' => [
                        'encodeLabels' => false,
                        'items' => [
                            [
                                'label' => Html::a('-12 month', '#', ['data-pjax' => 0]),
                                'options' => ['class' => 'add-remove-time', 'data-url' => '/premium/members/sub-time?id=' . $model->id, 'data-duration' => 31536000]
                            ],
                            [
                                'label' => Html::a('-6 month', '#', ['data-pjax' => 0]),
                                'options' => ['class' => 'add-remove-time', 'data-url' => '/premium/members/sub-time?id=' . $model->id, 'data-duration' => 15552000]
                            ],
                            [
                                'label' => Html::a('-3 month', '#', ['data-pjax' => 0]),
                                'options' => ['class' => 'add-remove-time', 'data-url' => '/premium/members/sub-time?id=' . $model->id, 'data-duration' => 7884000]
                            ],
                            [
                                'label' => Html::a('-1 month', '#', ['data-pjax' => 0]),
                                'options' => ['class' => 'add-remove-time', 'data-url' => '/premium/members/sub-time?id=' . $model->id, 'data-duration' => 2678400]
                            ],
                            [
                                'label' => Html::a('-1 week', '#', ['data-pjax' => 0]),
                                'options' => ['class' => 'add-remove-time', 'data-url' => '/premium/members/sub-time?id=' . $model->id, 'data-duration' => 604800]
                            ],
                            [
                                'label' => Html::a('-1 day', '#', ['data-pjax' => 0]),
                                'options' => ['class' => 'add-remove-time', 'data-url' => '/premium/members/sub-time?id=' . $model->id, 'data-duration' => 86400]
                            ],
                            [
                                'label' => Html::a('-1 hour', '#', ['data-pjax' => 0]),
                                'options' => ['class' => 'add-remove-time', 'data-url' => '/premium/members/sub-time?id=' . $model->id, 'data-duration' => 3600]
                            ],
                        ],
                    ],
                ]),
                ['class' => 'add-remove-time-container']
            );

            return "<div class=\"premium-left-container\">$formatted $add_remove_time_buttons</div>";
        },
        'filter' => [],
    ],
    [
        'attribute' => 'latest_transaction_date',
        'filterType' => GridView::FILTER_DATE,
        'format' => function ($value) {
            return date('M j, Y,  H:i:s', strtotime($value));
        },
        'label' => 'Last Payment',
        'width' => '180px',
        'filterWidgetOptions' => [
            'pluginOptions' => [
                'format' => 'yyyy-mm-dd',
                'autoclose' => true
            ]
        ],
    ],
    [
        'attribute' => 'created_at',
        'filterType' => GridView::FILTER_DATE,
        'format' => function ($value) {
            return date('M j, Y,  H:i:s', $value);
        },
        'label' => 'Registered At',
        'width' => '180px',
        'filterWidgetOptions' => [
            'pluginOptions' => [
                'format' => 'yyyy-mm-dd',
                'autoclose' => true
            ]
        ],
    ],
    [
        'class' => 'kartik\grid\BooleanColumn',
        'attribute' => 'status',
        'vAlign' => 'middle',
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => true,
        'order' => DynaGrid::ORDER_FIX_RIGHT,
        'template' => implode('', [
            '{view}',
            '{login-as}',
            '{hr}',
            '{fix-recurring}',
        ]),
        'buttons' => [
            'login-as' => function ($url, $model) {
                return Html::tag(
                    'li',
                    Html::a(
                        '<i class="glyphicon glyphicon-log-in"></i>Login As..',
                        env('FRONTEND_HOST_INFO') . '/account/auth?token=' . $model->auth_key,
                        ['target' => "_blank", 'data-pjax' => '0']
                    )
                );
            },
            'hr' => function () {
                return Html::tag('li', '', ['class' => 'divider']);
            },
            'fix-recurring' => function ($url, $model) {
                return Html::tag(
                    'li',
                    Html::a('<i class="glyphicon glyphicon-refresh"></i>'.'Fix Recurring', $url, ['data-user-email' => $model->email, 'data-fix-payment' => 1])
                );
            }
        ]
    ],
    ['class' => 'kartik\grid\CheckboxColumn', 'order' => DynaGrid::ORDER_FIX_RIGHT],
];

$toolbar = [
    '{export}',
    '{toggleData}',
    '{dynagrid}',
    [
        'content' => Html::button('Apply', [
            'id' => 'apply-bulk-actions',
            'class' => 'btn btn-default'
        ]),
        'options' => [
            'class' => 'btn-group pull-right'
        ]
    ],
    [
        'content' => Html::dropDownList('bulk-actions-list', 'Bulk Actions', [
            '' => 'Bulk Actions',
            'bulk-enable' => 'Enable',
            'bulk-disable' => 'Disable',
        ],
            [
                'id' => 'bulk-action-list',
                'class' => 'form-control',
            ]),
        'options' => [
            'class' => 'btn-group pull-right'
        ]
    ],
    [
        'content' =>
            Html::button('<i class="glyphicon glyphicon-user"></i> Add User', [
                'type' => 'button',
                'id' => 'register-new-member',
                'title' => 'Add Server',
                'class' => 'btn btn-success'
            ]),
        'options' => [
            'class' => 'btn-group pull-right'
        ]
    ],
];


echo DynaGrid::widget([
    'columns' => $columns,
    'storage' => DynaGrid::TYPE_COOKIE,
    'theme' => 'panel-info',
    'gridOptions' => [
        'itemLabelSingle' => 'member',
        'itemLabelPlural' => 'members',
        'showPageSummary' => false,
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'heading' => '<h3 class="panel-title">' . $this->title . '</h3>',
        ],
        'toolbar' => $toolbar,
        'hover' => true,
        'responsive' => false,
        'pjax' => true,
    ],
    'options' => ['id' => 'grid-premium-members-1']
]);

Modal::begin([
    'header' => 'Add Payment History Item',
    'size' => Modal::SIZE_SMALL,
    'options' => [
        'id' => 'add-new-history-item'
    ]
]);

echo $this->render('_form_add_payment_history', [
    'model' => new PremPaymentsHistory(),
    'enableAjaxValidation' => true
]);

Modal::end();



Modal::begin([
    'header' => 'ADD NEW USER',
    'size' => Modal::SIZE_SMALL,
    'options' => [
        'id' => 'add-new-user'
    ]
]);

echo $this->render('_form', [
    'model' => new PremSignupForm(),
    'enableAjaxValidation' => true
]);

Modal::end();
?>

<script>
    window.addEventListener('DOMContentLoaded', function () {
        $(document).on('click', '#register-new-member', function (e) {
            e.preventDefault();
            $('#add-new-user').modal('show');
        });

        $(document).on('click', '#submit-payment-item', function (e) {
            e.preventDefault();

            axios({
                method: 'post',
                url: $('#add-payment-history-form').attr('action'),
                data: $('#add-payment-history-form').serialize()
            }).then(function (response) {
                if (response.data.success) {
                    $(document).trigger('data-grid-reload-cell', [response.data.fields.id_prem_user]);
                }
            }).finally(function () {
                $('#add-new-history-item').modal('hide');
            })
        });

        $(document).on('click', '[data-add-history-item="1"]', function (e) {
            e.preventDefault();

            var userId = $(this).data('user-id');

            if (typeof(userId) === 'undefined') {
                alert('Missing user_id');
                return false;
            }

            $('#prempaymentshistory-id_prem_user').val(userId);
            $('#prempaymentshistory-paid_at').val('');

            $('#add-new-history-item').modal('show');
        });

        $(document).on('click', '[data-delete="1"]', function (e) {
            e.preventDefault();

            if (!confirm("Are you sure to delete payment history item?")) {
                return false;
            }

            var userId = $(this).data('user-id'),
                paymentId = $(this).data('payment-id');

            if (typeof(userId) === 'undefined' || typeof (paymentId) === 'undefined') {
                alert('Missing payment_id or used_id');
                return false;
            }

            axios({
                method: 'post',
                url: '/premium/members/delete-payment?id=' + userId,
                data: {
                    payment_id: paymentId
                }
            }).then(function () {
                $(document).trigger('data-grid-reload-cell', [parseInt(userId)]);
            }).catch(function (err) {
                alert(err);
            });
        });

        $(document).on('click', '.add-remote-time a', function (e) {
            e.preventDefault();
        });

        $(document).on('click', '.add-remove-time', async function (e) {
            e.preventDefault();

            try {
                let response = await axios.post(this.dataset.url, {
                    duration: this.dataset.duration
                });

                $.pjax.reload({container: "#grid-premium-members-1-pjax"});
            } catch (e) {
                $.notify('error', e);
            }
        });
    });
</script>

<style>
    #grid-premium-members-1 td {
        cursor: pointer;
        vertical-align: middle;
    }

    .dropdown-menu .add-remove-time.dropdown-header {
        padding: 0;
    }

    .dropdown-menu .add-remove-time a {
        color: #333;
        text-align: left;
        font-size: 14px;
    }
</style>
