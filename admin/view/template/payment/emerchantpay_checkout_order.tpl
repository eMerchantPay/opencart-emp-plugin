<div class="panel-group" id="accordion">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                    <span class="glyphicon glyphicon-folder-close"></span>
                    <?php echo $text_payment_info; ?></a>
            </h4>
        </div>
        <div id="collapseOne" class="panel-collapse collapse in">
            <table class="table table-hover tree">
                <thead>
                <tr>
                    <th><?php echo $text_transaction_id; ?></th>
                    <th><?php echo $text_transaction_type; ?></th>
                    <th><?php echo $text_transaction_timestamp; ?></th>
                    <th><?php echo $text_transaction_amount; ?></th>
                    <th><?php echo $text_transaction_status; ?></th>
                    <th><?php echo $text_transaction_message; ?></th>
                    <th><?php echo $text_transaction_mode; ?></th>
                    <th><?php echo $text_transaction_action; ?></th>
                    <th><?php echo $text_transaction_action; ?></th>
                    <th><?php echo $text_transaction_action; ?></th>
                </tr>
                </thead>

                <tbody>
                    <?php foreach ($transactions as $transaction) { ?>
                        <tr class="treegrid-<?php echo $transaction['unique_id'];?> <?php if(strlen($transaction['reference_id']) > 1): ?> treegrid-parent-<?php echo $transaction['reference_id'];?> <?php endif;?>">
                            <td class="text-left">
                                <?php echo $transaction['unique_id'];?>
                            </td>
                            <td class="text-left">
                                <?php echo $transaction['type']; ?>
                            </td>
                            <td class="text-left">
                                <?php echo $transaction['timestamp']; ?>
                            </td>
                            <td class="text-left">
                                <?php echo $transaction['amount']; ?>
                            </td>
                            <td class="text-left">
                                <?php echo $transaction['status']; ?>
                            </td>
                            <td class="text-left">
                                <?php echo $transaction['message']; ?>
                            </td>
                            <td class="text-left">
                                <?php echo $transaction['mode']; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($transaction['can_capture']) { ?>
                                    <div class="transaction-action-button">
                                        <a class="button btn btn-success" id="button-capture" role="button" data-type="capture" data-reference-id="<?php echo $transaction['unique_id'];?>">
                                            <i class="fa fa-check fa-lg"></i>
                                        </a>
                                        <span class="btn btn-primary" id="img_loading_capture" style="display:none;">
                                            <i class="fa fa-circle-o-notch fa-spin fa-lg"></i>
                                        </span>
                                    </div>
                                <?php } ?>
                            </td>
                            <td class="text-center">
                                <?php if ($transaction['can_refund']) { ?>
                                    <div class="transaction-action-button">
                                        <a class="button btn btn-warning" id="button-refund" role="button" data-type="refund" data-reference-id="<?php echo $transaction['unique_id'];?>">
                                            <i class="fa fa-reply fa-lg"></i>
                                        </a>
                                        <span class="btn btn-primary" id="img_loading_rebate" style="display:none;">
                                            <i class="fa fa-circle-o-notch fa-spin fa-lg"></i>
                                        </span>
                                    </div>
                                <?php } ?>
                            </td>
                            <td class="text-center">
                                <?php if ($transaction['can_void']) { ?>
                                    <div class="transaction-action-button">
                                        <a class="button btn btn-danger" id="button-void" role="button" data-type="void" data-reference-id="<?php echo $transaction['unique_id'];?>">
                                            <i class="fa fa-remove fa-lg"></i>
                                        </a>
                                        <span class="btn btn-primary" id="img_loading_void" style="display:none;">
                                            <i class="fa fa-circle-o-notch fa-spin fa-lg"></i>
                                        </span>
                                    </div>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" role="dialog"></div>
<style>
    .transaction-action-button { display:inline-block; }
</style>
<script type="text/javascript">
    $("#button-capture, #button-refund, #button-void").click(function() {
        $.ajax({
            url: '<?php echo $url_modal;?>',
            type: 'POST',
            data: {
                'order_id'      : '<?php echo $order_id;?>',
                'reference_id'  : jQuery(this).attr('data-reference-id'),
                'type'          : jQuery(this).attr('data-type')
            },
            dataType: 'html',
            success: function(data) {
                $('.modal.fade').html(data).modal('show');
            }
        });
    });
    $(document).ready(function() {
        $('.modal').on('hidden', function(){
            console.log('CLOSEE');
        });
        $('.tree').treegrid();
    });
</script>