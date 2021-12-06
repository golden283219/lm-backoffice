<?php
/**
 * @var $payments array
 * @var $model backend\models\PremUsers
 */
?>

<div class="row">
    <div class="col-md-10">
        <table class="table table-bordered">
            <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Date</th>
                <th scope="col">Payment #</th>
                <th scope="col">Payment Method</th>
                <th scope="col">Price</th>
                <th scope="col">Status</th>
                <th scope="col">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($payments as $index => $payment): ?>
            <tr>
                <td>
                    <?php echo ++$index; ?>
                </td>
                <td>
                    <?php echo date("d/M/Y", $payment->created_at); ?>
                </td>
                <td>
                    <?php echo $payment->id + 10000; ?>
                </td>
                <td>
                    <?php echo $payment::get_method_label($payment->payment_method); ?>
                </td>
                <td>
                    <?php echo $payment->paid_usd . ' $'; ?>
                </td>
                <td>
                    <?php echo isset($payment::$history_status_vocabulary[$payment->payment_status]) ?
                        $payment::$history_status_vocabulary[$payment->payment_status] :
                        '';
                    ?>
                </td>
                <td style="text-align: center;">
                    <a href="/premium/members/delete-payment-history?id=<?= $payment->id; ?>" data-user-id="<?= $payment->id_prem_user; ?>" data-delete="1" data-payment-id="<?= $payment->id; ?>">
                        <span class="glyphicon glyphicon-trash"></span>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <button type="button" class="btn btn-primary" data-add-history-item="1" data-user-id="<?= $model->id; ?>" style="width: 100%;">
                Add Record
            </button>
        </div>
    </div>
</div>
