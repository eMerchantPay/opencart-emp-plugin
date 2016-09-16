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

// Heading
$_['heading_title'] = 'eMerchantPay Direct';

// Text
$_['text_payment']  = 'Payment';
$_['text_success']  = 'Success: You have modified your eMerchantPay configuration!';
$_['text_failed']   = 'Failed: An error has occured saving your eMerchantPay configuration!';
$_['text_edit']     = 'Edit eMerchantPay Direct';
$_['text_yes']      = 'Yes';
$_['text_no']       = 'No';
$_['text_required'] = 'This field is required!';
$_['text_emerchantpay_direct']
                    = <<<HTML
<a href="https://www.emerchantpay.com/" target="_blank">
    <img src="view/image/payment/emerchantpay.png" alt="eMerchantPay" title="eMerchantPay" style="border: 1px solid #EEEEEE;" />
</a>
HTML;

// Entry
$_['entry_username']                 = 'Genesis Username';
$_['entry_password']                 = 'Genesis Password';
$_['entry_token']                    = 'Genesis Token';
$_['entry_sandbox']                  = 'Test Mode';
$_['entry_transaction_type']         = 'Transaction Type';
$_['entry_supports_partial_capture'] = 'Partial Capture';
$_['entry_supports_partial_refund']  = 'Partial Refund';
$_['entry_supports_void']            = 'Cancel Transaction';
$_['entry_total']                    = 'Total';
$_['entry_order_status']             = 'Order Status';
$_['entry_async_order_status']       = 'Order Status (Async)';
$_['entry_order_status_failure']     = 'Order Status (Failed)';
$_['entry_geo_zone']                 = 'Geo Zone';
$_['entry_status']                   = 'Status';
$_['entry_debug']                    = 'Error Logging';
$_['entry_sort_order']               = 'Sort Order';

// Transaction Types
$_['text_transaction_authorize']         = 'Authorize';
$_['text_transaction_authorize_3d']      = 'Authorize 3D-Secure';
$_['text_transaction_sale']              = 'Sale (Authorize + Capture)';
$_['text_transaction_sale_3d']           = 'Sale (Authorize + Capture) 3D-Secure';
$_['text_transaction_init_recurring']    = 'Recurring';
$_['text_transaction_init_recurring_3d'] = 'Recurring 3D-Secure';

// Transaction View
$_['text_payment_info']          = 'eMerchantPay transactions';
$_['text_transaction_id']        = 'Transaction ID';
$_['text_transaction_timestamp'] = 'Date/Time';
$_['text_transaction_amount']    = 'Amount';
$_['text_transaction_status']    = 'Status';
$_['text_transaction_type']      = 'Type';
$_['text_transaction_message']   = 'Message';
$_['text_transaction_mode']      = 'Mode';
$_['text_transaction_action']    = 'Action';

// Modal View
$_['text_button_close']           = 'Close';
$_['text_button_capture_partial'] = 'Capture';
$_['text_button_capture_full']    = 'Capture Full Amount';
$_['text_button_refund_partial']  = 'Refund';
$_['text_button_refund_full']     = 'Refund Full Amount';
$_['text_button_void']            = 'Cancel Transaction';

$_['text_modal_title_capture'] = 'Capture transaction';
$_['text_modal_title_refund']  = 'Refund transaction';
$_['text_modal_title_void']    = 'Cancel transaction';

// User JSON statuses
$_['text_invalid_reference_id'] = 'Invalid Reference Id (target transaction)!';
$_['text_invalid_transaction']  = 'Invalid Request!';

// Status
$_['text_response_success'] = 'Transaction completed successfully.';
$_['text_response_failure'] = 'Transaction has failed. Check your parameters/credentials';
$_['text_response_capture'] = 'Capture transaction completed successfully';
$_['text_response_refund']  = 'Refund transaction completed successfully';
$_['text_response_void']    = 'Void transaction completed successfully';

// Help
$_['help_total']                                     = 'Minimum Order Amount required, in order to activate this payment method.';
$_['help_sandbox']                                   = 'Use Sandbox (Test) or Production (Live) environment.';
$_['help_order_status']                              = 'Order status for successfully completed transactions';
$_['help_async_order_status']                        = 'Order status for initiated asynchronous (3D) transaction';
$_['help_failure_order_status']                      = 'Order status for failed transactions';
$_['help_supports_partial_capture']                  = "Use this option to allow / deny Partial Capture Transactions";
$_['help_supports_partial_refund']                   = "Use this option to allow / deny Partial Refund Transactions";
$_['help_supports_void']                             = "Use this option to allow / deny Cancel Transactions";
$_['help_transaction_option_capture_partial_denied'] = "Partial Capture is currently disabled!";
$_['help_transaction_option_refund_partial_denied']  = "Partial Refund is currently disabled!";
$_['help_transaction_option_cancel_denied']          = "Cancel Transaction are currently disabled. You can enable this option in the Module Settings.";


// Error
$_['error_permission']       = 'Warning: You do not have permission to modify payment module eMerchantPay!';
$_['error_username']         = 'Genesis Username is Required!';
$_['error_password']         = 'Genesis Password is Required!';
$_['error_token']            = 'Genesis Token is Required!';
$_['error_transaction_type'] = 'You have to select at least one transaction type!';
$_['error_https']            = 'This payment method requires HTTPS connection in order to process payment data!';
$_['error_controls_invalidated'] = 'Warning: You have to fill-in all the required fields';
