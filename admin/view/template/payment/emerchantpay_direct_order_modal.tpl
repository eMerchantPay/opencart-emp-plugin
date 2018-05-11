<?php
/*
 * Copyright (C) 2018 emerchantpay Ltd.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @author      emerchantpay
 * @copyright   2018 emerchantpay Ltd.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2 (GPL-2.0)
 */
?>

<div class="modal-dialog">
    <div class="modal-content">
        <?php if ($type == 'capture') { ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <span class="emerchantpay-logo"><h4 id="myModalLabel"><?php echo $text_modal_title_capture; ?></h4></span>
            </div>
            <div class="modal-body">
                <div class="alert alert-notification alert-dismissible">
                    <i class="fa fa-info-circle"></i>
                    <span class="alert-text"></span>
                    <button type="button" class="close" data-hide="alert-notification">&times;</button>
                </div>
                <form class="form modal-form" action="<?php echo $url_action; ?>" method="post">
                    <input type="hidden" name="order_id" value="<?php echo $transaction['order_id']; ?>" />
                    <input type="hidden" name="reference_id" value="<?php echo $transaction['unique_id'];?>" />

                    <?php if (!$emerchantpay_direct_supports_partial_capture) { ?>
                        <div id="<?php echo $module_name;?>_capture_trans_info" class="row">
                            <div class="col-xs-12">
                                <div class="alert alert-info">
                                    You are allowed to process only full capture through this panel!
                                    <br/>
                                    This option can be enabled in the <strong>Module Settings</strong>, but it depends on the <strong>acquirer</strong>.
                                    For further Information please contact your <strong>Account Manager</strong>.
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="form-group">
                        <label for="<?php echo $module_name;?>_transaction_amount">Capture amount:</label>
                        <div class="input-group">
                            <span class="input-group-addon" data-toggle="<?php echo $module_name;?>-tooltip" data-placement="top" title="<?php echo $currency['iso_code'];?>"><?php echo $currency['sign'];?></span>
                            <input type="text" class="form-control" id="<?php echo $module_name;?>_transaction_amount"
                                <?php if (!$emerchantpay_direct_supports_partial_capture) { ?>
                                    data-toggle="<?php echo $module_name;?>-tooltip" data-placement="bottom" title="<?php echo $help_transaction_option_capture_partial_denied;?>"
                                <?php } ?>
                                name="amount" placeholder="Capture amount..." value="<?php echo $transaction['available_amount']; ?>" />
                        </div>
                        <span class="help-block" id="<?php echo $module_name;?>-amount-error-container"></span>
                    </div>
                    <div class="form-group">
                        <label for="<?php echo $module_name;?>_transaction_message">Transaction Message (optional):</label>
                        <textarea class="form-control" rows="3" id="<?php echo $module_name;?>_transaction_message" name="message"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                     <span class="form-loading">
                        <i class="fa fa-spinner fa-spin fa-lg"></i>
                    </span>
                    <span class="form-buttons">
                        <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo $text_button_close;?></button>
                        <button class="btn btn-submit btn-info btn-capture" value="partial"><?php echo $text_button_capture_partial;?></button>
                    </span>
            </div>
        <?php }
        else if ($type == 'refund') { ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <span class="emerchantpay-logo"><h4 id="myModalLabel"><?php echo $text_modal_title_refund; ?></h4></span>
            </div>
            <div class="modal-body">
                <div class="alert alert-notification alert-dismissible">
                    <i class="fa fa-info-circle"></i>
                    <span class="alert-text"></span>
                    <button type="button" class="close" data-hide="alert-notification">&times;</button>
                </div>
                <form class="form modal-form" action="<?php echo $url_action; ?>" method="post">
                    <input type="hidden" name="order_id" value="<?php echo $transaction['order_id']; ?>" />
                    <input type="hidden" name="reference_id" value="<?php echo $transaction['unique_id'];?>" />

                    <?php if (!$emerchantpay_direct_supports_partial_refund) { ?>
                    <div id="<?php echo $module_name;?>_capture_trans_info" class="row">
                        <div class="col-xs-12">
                            <div class="alert alert-info">
                                You are allowed to process only full refund through this panel!
                                <br/>
                                This option can be enabled in the <strong>Module Settings</strong>, but it depends on the <strong>acquirer</strong>.
                                For further Information please contact your <strong>Account Manager</strong>.
                            </div>
                        </div>
                    </div>
                    <?php } ?>

                    <div class="form-group">
                        <label for="<?php echo $module_name;?>_transaction_amount">Refund amount:</label>
                        <div class="input-group">
                            <span class="input-group-addon" data-toggle=<?php echo $module_name;?>-tooltip" data-placement="top" title="<?php echo $currency['iso_code'];?>"><?php echo $currency['sign'];?></span>
                            <input type="text" class="form-control" id="<?php echo $module_name;?>_transaction_amount"
                            <?php if (!$emerchantpay_direct_supports_partial_refund) { ?>
                            data-toggle="<?php echo $module_name;?>-tooltip" data-placement="bottom" title="<?php echo $help_transaction_option_refund_partial_denied;?>"
                            <?php } ?>
                            name="amount" placeholder="Refund amount..." value="<?php echo $transaction['available_amount']; ?>" />
                        </div>
                        <span class="help-block" id="<?php echo $module_name;?>-amount-error-container"></span>
                    </div>
                    <div class="form-group">
                        <label for="<?php echo $module_name;?>_transaction_message">Transaction Message (optional):</label>
                        <textarea class="form-control" rows="3" id="<?php echo $module_name;?>_transaction_message" name="message"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                     <span class="form-loading">
                        <i class="fa fa-spinner fa-spin fa-lg"></i>
                    </span>
                    <span class="form-buttons">
                        <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo $text_button_close;?></button>
                        <button class="btn btn-submit btn-info btn-refund"><?php echo $text_button_refund_partial;?></button>
                    </span>
            </div>
        <?php }
        else if ($type == 'void') { ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <span class="emerchantpay-logo"><h4 id="myModalLabel"><?php echo $text_modal_title_void; ?></h4></span>
            </div>
            <div class="modal-body">
                <div class="alert alert-notification alert-dismissible">
                    <i class="fa fa-info-circle"></i>
                    <span class="alert-text"></span>
                    <button type="button" class="close" data-dismiss="alert-notification" aria-hidden="true">&times;</button>
                </div>
                <p class="text-center">
                    Are you sure you want to Void (cancel):
                    <br/>
                    <span style="text-decoration: underline;">
                        <?php echo ucfirst($transaction['type']);?>
                    </span>
                    transaction with UniqueId:
                    <span style="text-decoration: underline;">
                        <?php echo $transaction['unique_id']; ?>
                    </span>
                </p>
                <form class="form modal-form" action="<?php echo $url_action; ?>" method="post">
                    <input type="hidden" name="order_id" value="<?php echo $transaction['order_id']; ?>" />
                    <input type="hidden" name="reference_id" value="<?php echo $transaction['unique_id'];?>" />

                    <?php if ($transaction['is_allowed']) { ?>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="alert alert-warning">
                                    This service is only available for particular acquirers!
                                    <br/>
                                    For further Information please contact your Account Manager.
                                </div>
                            </div>
                        </div>
                    <?php }
                    else { ?>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="alert alert-danger">
                                    There is already an approved <strong>Cancel Transaction</strong>
                                    for <strong><?php echo ucfirst($transaction['type']);?> Transaction</strong> with
                                    Unique Id: <strong><?php echo $transaction['unique_id'];?></strong>
                                    <br/>
                                    You are not allowed to send this Cancel Transaction. <br /> For further Information please contact your Account Manager.
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="form-group">
                        <label for="<?php echo $module_name;?>_transaction_message">Transaction Message (optional):</label>
                        <textarea class="form-control" rows="3" id="<?php echo $module_name;?>_transaction_message;?>" name="message"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                    <span class="form-loading">
                        <i class="fa fa-spinner fa-spin fa-lg"></i>
                    </span>
                    <span class="form-buttons">
                        <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo $text_button_close;?></button>
                        <button class="btn btn-submit btn-primary btn-void"><?php echo $text_button_void;?></button>
                    </span>
            </div>
        <?php } ?>
    </div>
</div>

<script type="text/javascript">
    $('.btn-submit').click(function() {
        $(this).parents().eq(2).find('.modal-form').submit();
    });

    $('form.modal-form').submit(function() {
        var $modalForm   = $(this);
        var $modalDialog = $(this).parents().eq(4).find('.modal-dialog');

        $.ajax({
            url:    $modalForm.attr('action'),
            type:   $modalForm.attr('method'),
            data:   $modalForm.serialize(),
            beforeSend: function () {
                $modalDialog.find('.form-buttons').hide();
                $modalDialog.find('.form-loading').show();
            },
            complete: function() {
                $modalDialog.find('.form-loading').hide();
                $modalDialog.find('.form-buttons').show();
            },
            success: function (data) {
                if (data.text !== undefined && data.text.length > 0) {
                    $modalDialog.find('.alert-notification .alert-text').text(data.text);
                    $modalDialog.find('.alert-notification').removeClass('alert-danger').addClass('alert-info').slideDown();
                }

                $modalDialog.find('.form-buttons button.btn-submit').prop('disabled', true);

                setTimeout(function() {
                    $('.modal.fade').modal('hide');
                    location.reload();
                }, 3000);
            },
            error: function(xhr) {
                if (xhr.responseJSON !== undefined && xhr.responseJSON.text !== undefined && xhr.responseJSON.text.length > 0) {
                    error = xhr.responseJSON.text;
                }
                else {
                    error = xhr.statusText;
                }

                $modalDialog.find('.alert-notification .alert-text').text(error);
                $modalDialog.find('.alert-notification').removeClass('alert-info').addClass('alert-danger').slideDown();
            }
        });

        // prevent re-submitting
        return false;
    });

    $(function(){
        $("[data-hide]").on("click", function(){
            $("." + $(this).attr("data-hide")).slideUp();
        });

        $('[data-toggle="<?php echo $module_name;?>-tooltip"]').tooltip();

        $('#<?php echo $module_name;?>_transaction_amount').number(true,
                modalPopupDecimalValueFormatConsts.decimalPlaces,
                modalPopupDecimalValueFormatConsts.decimalSeparator,
                modalPopupDecimalValueFormatConsts.thousandSeparator
        );

        <?php if (($type == 'capture') || ($type == 'refund')) {
            if (($type == 'capture' && !$emerchantpay_direct_supports_partial_capture) ||
            ($type == 'refund' && !$emerchantpay_direct_supports_partial_refund)) { ?>
            $('#<?php echo $module_name;?>_transaction_amount').attr('readonly', 'readonly');
            <?php } ?>
            <?php }
        else if (($type == 'void') && (!$transaction['is_allowed'] || !$emerchantpay_direct_supports_void)) { ?>
            $('.btn-submit').attr('disabled', 'disabled');
        <?php } ?>

    });

</script>
