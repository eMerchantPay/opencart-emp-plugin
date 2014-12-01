<h4><?php echo $text_payment_info; ?></h4>
<div class="alert alert-success" id="sagepay_direct_transaction_msg" style="display:none;"></div>
<table class="table table-striped table-bordered">
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
    <tr id="trx-<?php echo $transaction['unique_id'];?>">
        <td class="text-left"><?php echo $transaction['unique_id'];?></td>
        <td class="text-left"><?php echo $transaction['type']; ?></td>
        <td class="text-left"><?php echo $transaction['timestamp']; ?></td>
        <td class="text-left"><?php echo $transaction['amount']; ?></td>
        <td class="text-left"><?php echo $transaction['status']; ?></td>
        <td class="text-left"><?php echo $transaction['message']; ?></td>
        <td class="text-left"><?php echo $transaction['mode']; ?></td>
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
        </td><td class="text-center">
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
        </td><td class="text-center">
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
<div class="modal fade" role="dialog"></div>
<style>
    .transaction-action-button { display:inline-block; }
</style>
<script type="text/javascript"><!--

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
    });

    /*
    $("#button-capture").click(function() {
        $('#captureForm form').append('<input type="hidden" name="reference_id" value="'. jQuery(this).attr('data-reference').' />');
        $('#captureForm form').append('<input type="hidden" name="reference_id" value="'. jQuery(this).attr('data-reference').' />');
        $('#captureForm').modal('show');
    });

    $("#button-refund").click(function() {
        $('#refundForm input[name="reference_id"]').val(jQuery(this).attr('data-reference'));
        $('#refundForm').modal('show');
    });

    $("#button-void").click(function() {
        $('#voidForm input[name="reference_id"]').val(jQuery(this).attr('data-reference'));
        $('#voidForm').modal('show');
    });

    $("#button-void").click(function() {
        if (confirm('<?php echo $text_confirm_void; ?>')) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: {'order_id': <?php echo $order_id; ?>},
                url: 'index.php?route=payment/emerchantpay_direct/void&token=<?php echo $token; ?>',
                beforeSend: function() {
                    $('#button-void').hide();
                    $('#img_loading_void').show();
                    $('#sagepay_direct_transaction_msg').hide();
                },
                success: function(data) {
                    if (data.error == false) {
                        html = '';
                        html += '<tr>';
                        html += '<td class="text-left">' + data.data.date_added + '</td>';
                        html += '<td class="text-left">void</td>';
                        html += '<td class="text-left">0.00</td>';
                        html += '</tr>';

                        $('.void_text').text('<?php echo $text_yes; ?>');
                        $('#sagepay_direct_transactions').append(html);
                        $('#button-release').hide();
                        $('#release_amount').hide();

                        if (data.msg != '') {
                            $('#sagepay_direct_transaction_msg').empty().html('<i class="fa fa-check-circle"></i> ' + data.msg).fadeIn();
                        }
                    }
                    if (data.error == true) {
                        alert(data.msg);
                        $('#button-void').show();
                    }

                    $('#img_loading_void').hide();
                }
            });
        }
    });
    $("#button-release").click(function() {
        if (confirm('<?php echo $text_confirm_release; ?>')) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: {'order_id': <?php echo $order_id; ?>, 'amount': $('#release_amount').val()},
                url: 'index.php?route=payment/emerchantpay_direct/release&token=<?php echo $token; ?>',
                beforeSend: function() {
                    $('#button-release').hide();
                    $('#release_amount').hide();
                    $('#img_loading_release').show();
                    $('#sagepay_direct_transaction_msg').hide();
                },
                success: function(data) {
                    if (data.error == false) {
                        html = '';
                        html += '<tr>';
                        html += '<td class="text-left">' + data.data.date_added + '</td>';
                        html += '<td class="text-left">payment</td>';
                        html += '<td class="text-left">' + data.data.amount + '</td>';
                        html += '</tr>';

                        $('#sagepay_direct_transactions').append(html);
                        $('#sagepay_direct_total_released').text(data.data.total);

                        if (data.data.release_status == 1) {
                            $('#button-void').hide();
                            $('.release_text').text('<?php echo $text_yes; ?>');
                        } else {
                            $('#button-release').show();
                            $('#release_amount').val(0.00);

                            <?php //if ($auto_settle == 2) { ?>
                                //$('#release_amount').show();
                            <?php //} ?>
                        }

                        if (data.msg != '') {
                            $('#sagepay_direct_transaction_msg').empty().html('<i class="fa fa-check-circle"></i> ' + data.msg).fadeIn();
                        }

                        $('#button-rebate').show();
                        $('#rebate_amount').val(0.00).show();
                    }
                    if (data.error == true) {
                        alert(data.msg);
                        $('#button-release').show();
                        $('#release_amount').show();
                    }

                    $('#img_loading_release').hide();
                }
            });
        }
    });
    $("#button-rebate").click(function() {
        if (confirm('<?php echo $text_confirm_rebate ?>')) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: {'order_id': <?php echo $order_id; ?>, 'amount': $('#rebate_amount').val()},
                url: 'index.php?route=payment/emerchantpay_direct/rebate&token=<?php echo $token; ?>',
                beforeSend: function() {
                    $('#button-rebate').hide();
                    $('#rebate_amount').hide();
                    $('#img_loading_rebate').show();
                    $('#sagepay_direct_transaction_msg').hide();
                },
                success: function(data) {
                    if (data.error == false) {
                        html = '';
                        html += '<tr>';
                        html += '<td class="text-left">' + data.data.date_added + '</td>';
                        html += '<td class="text-left">rebate</td>';
                        html += '<td class="text-left">' + data.data.amount + '</td>';
                        html += '</tr>';

                        $('#sagepay_direct_transactions').append(html);
                        $('#sagepay_direct_total_released').text(data.data.total_released);

                        if (data.data.rebate_status == 1) {
                            $('.rebate_text').text('<?php echo $text_yes; ?>');
                        } else {
                            $('#button-rebate').show();
                            $('#rebate_amount').val(0.00).show();
                        }

                        if (data.msg != '') {
                            $('#sagepay_direct_transaction_msg').empty().html('<i class="fa fa-check-circle"></i> ' + data.msg).fadeIn();
                        }
                    }
                    if (data.error == true) {
                        alert(data.msg);
                        $('#button-rebate').show();
                    }

                    $('#img_loading_rebate').hide();
                }
            });
        }
    });
     */
    //--></script>