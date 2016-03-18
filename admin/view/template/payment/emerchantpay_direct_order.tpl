<?php
/*
 * Copyright (C) 2016 eMerchantPay Ltd.
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
 * @author      eMerchantPay
 * @copyright   2016 eMerchantPay Ltd.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2 (GPL-2.0)
 */
?>

<div class="panel-group" id="accordion">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                    <span class="emerchantpay-logo"><?php echo $text_payment_info; ?></span>
                </a>
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
                        <td class="text-right">
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
                                    <a class="button btn btn-danger" id="button-void"
                                        <?php if (!$emerchantpay_direct_supports_void) { ?>
                                            data-toggle="tooltip" data-placement="bottom" title="<?php echo $help_transaction_option_cancel_denied;?>"
                                        <?php }
                                        else if ($transaction['void_exists']) { ?>
                                            data-toggle="tooltip" data-placement="bottom"
                                            title="There is already an approved <strong>Cancel Transaction</strong> for <strong><?php echo ucfirst($transaction['type']);?> Transaction</strong> with Unique Id: <strong><?php echo $transaction['unique_id'];?></strong>"
                                        <?php } ?>

                                        <?php if (!$emerchantpay_direct_supports_void || $transaction['void_exists']) { ?>
                                            disabled="disabled"
                                        <?php } ?>

                                        role="button" data-type="void" data-reference-id="<?php echo $transaction['unique_id'];?>">
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

<script type="text/javascript">
    var modalPopupDecimalValueFormatConsts = {
        decimalPlaces       : <?php echo $currency['decimalPlaces'];?>,
    decimalSeparator    : "<?php echo $currency['decimalSeparator'];?>",
            thousandSeparator   : "<?php echo $currency['thousandSeparator'];?>"
    };

    (function($) {
        jQuery.exists = function(selector) {
            return ($(selector).length > 0);
        }
    }(window.jQuery));

    $("#button-capture, #button-refund, #button-void").click(function() {
        if (jQuery(this).is('[disabled]'))
            return ;

        $.ajax({
            url: '<?php echo $url_modal;?>',
            type: 'post',
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
        $('.tree').treegrid({
            expanderExpandedClass:  'treegrid-expander-expanded',
            expanderCollapsedClass: 'treegrid-expander-collapsed'
        });

        var modalObj = $('.modal.fade');

        modalObj.on('hide.bs.modal', function() {
            destroyBootstrapValidator(modalObj);
        });

        modalObj.on('shown.bs.modal', function() {
            /* enable the submit button just in case (if the bootstrapValidator is enabled it will disable the button if necessary */
            var transactionType = modalObj.find('.modal-dialog').attr('data-type');

            if (transactionType !== 'void') {
                $('.form-buttons button.btn-submit').removeAttr('disabled');


                if (createBootstrapValidator('form.modal-form')) {
                    executeBootstrapFieldValidator('form.modal-form', 'fieldAmount');
                }
            }
        });
    });

    function destroyBootstrapValidator(submitForm) {
        submitForm.bootstrapValidator('destroy');
    }

    function createBootstrapValidator(submitFormSelector) {
        var submitForm = $(submitFormSelector);
        var transactionAmountControlSelector = '#<?php echo $module_name;?>_transaction_amount';

        var transactionAmount = formatTransactionAmount($(transactionAmountControlSelector).val());

        destroyBootstrapValidator(submitForm);


        var shouldCreateValidator = $.exists(transactionAmountControlSelector);

        /* it is not needed to create attach the bootstapValidator, when the field to validate is not visible (Void Transaction) */
        if (!shouldCreateValidator)
            return false;

        submitForm.bootstrapValidator({
                    fields: {
                        fieldAmount: {
                            selector: transactionAmountControlSelector,
                            container: '#<?php echo $module_name;?>-amount-error-container',
                            validators: {
                                notEmpty: {
                                    message: 'The transaction amount is a required field!'
                                },
                                stringLength: {
                                    max: 10
                                },
                                greaterThan: {
                                    value: 0,
                                    inclusive: false
                                },
                                lessThan: {
                                    value: transactionAmount,
                                    inclusive: true
                                }
                            }
                        }
                    }
                })
                .on('error.field.bv', function(e, data) {
                    $('.form-buttons button.btn-submit').attr('disabled', 'disabled');
                })
                .on('success.field.bv', function(e) {
                    $('.form-buttons button.btn-submit').removeAttr('disabled');
                })
                .on('success.form.bv', function(e) {
                    e.preventDefault(); // Prevent the form from submitting

                    /* submits the transaction form (No validators have failed) */
                    //submitForm.bootstrapValidator('defaultSubmit');
                });

        return true;
    }

    function executeBootstrapFieldValidator(formSelector, validatorFieldName) {
        var submitForm = $(formSelector);

        submitForm.bootstrapValidator('validateField', validatorFieldName);
        submitForm.bootstrapValidator('updateStatus', validatorFieldName, 'NOT_VALIDATED');
    }

    function formatTransactionAmount(amount) {
        if ((typeof amount == 'undefined') || (amount == null))
            amount = 0;

        return $.number(amount, modalPopupDecimalValueFormatConsts.decimalPlaces,
                modalPopupDecimalValueFormatConsts.decimalSeparator,
                modalPopupDecimalValueFormatConsts.thousandSeparator);
    }

</script>