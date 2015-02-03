<?php
// Heading
$_['heading_title']					= 'eMerchantPay Direct';

// Text
$_['text_payment']					= 'Payment';
$_['text_success']					= 'Success: You have modified your eMerchantPay configuration!';
$_['text_edit']                     = 'Edit eMerchantPay';
$_['text_emerchantpay_direct']		= '<a href="https://www.emerchantpay.com/" target="_blank"><img src="view/image/payment/emerchantpay.png" alt="eMerchantPay" title="eMerchantPay" style="border: 1px solid #EEEEEE;" /></a>';
$_['text_yes']                      = 'Yes';
$_['text_no']                       = 'No';

// Entry
$_['entry_username']				= 'Genesis Username';
$_['entry_password']				= 'Genesis Password';
$_['entry_token']					= 'Genesis Token';
$_['entry_sandbox']					= 'Test Mode';
$_['entry_transaction_type']        = 'Transaction Type';
$_['entry_total']					= 'Total';
$_['entry_order_status']			= 'Order Status (Completed)';
$_['entry_async_order_status']		= 'Order Status (Async)';
$_['entry_failure_order_status']    = 'Order Status (Failed)';
$_['entry_geo_zone']				= 'Geo Zone';
$_['entry_status']					= 'Status';
$_['entry_debug']                   = 'Error Logging';
$_['entry_sort_order']				= 'Sort Order';

// Help
$_['help_total']                    = 'Minimum Total Amount for which this mode is active.';
$_['help_sandbox']                  = 'Use Sandbox (Test) or Production (Live) gateway.<br/><br/>Keep in mind, that by Sandbox (Test) Gateway - NO MONEY ARE BEING TRANSFERRED.';
$_['help_order_status']             = 'Order status for successfully completed transactions';
$_['help_async_order_status']       = 'Order status for initiated asynchronous (3D) transaction';
$_['help_failure_order_status']     = 'Order status for failed transactions';

// Transaction Types
$_['text_transaction_authorize']            = 'Authorize';
$_['text_transaction_authorize_3d']         = 'Authorize 3D';
$_['text_transaction_sale']                 = 'Sale (Authorize + Capture)';
$_['text_transaction_sale_3d']              = 'Sale 3D (Authorize + Capture)';
$_['text_transaction_init_recurring']       = 'Recurring';
$_['text_transaction_init_recurring_3d']    = 'Recurring 3D';

// Transaction View
$_['text_payment_info']             = 'eMerchantPay transactions';
$_['text_transaction_id']           = 'Transaction ID';
$_['text_transaction_timestamp']    = 'Date/Time';
$_['text_transaction_amount']       = 'Amount';
$_['text_transaction_status']       = 'Status';
$_['text_transaction_type']         = 'Type';
$_['text_transaction_message']      = 'Message';
$_['text_transaction_mode']         = 'Mode';
$_['text_transaction_action']       = 'Action';

// Modal View
$_['text_button_close']             = 'Close';
$_['text_button_capture_partial']   = 'Capture';
$_['text_button_capture_full']      = 'Capture Full Amount';
$_['text_button_refund_partial']    = 'Refund';
$_['text_button_refund_full']       = 'Refund Full Amount';
$_['text_button_void']              = 'Cancel Transaction';

$_['text_modal_title_capture']      = 'Capture transaction';
$_['text_modal_title_refund']       = 'Refund transaction';
$_['text_modal_title_void']         = 'Cancel transaction';

// User JSON statuses
$_['text_invalid_reference_id']     = 'Invalid Reference Id (target transaction)!';
$_['text_invalid_transaction']      = 'Invalid Request!';

// Status
$_['text_response_success']         = 'Transaction completed successfully.';
$_['text_response_failure']         = 'Transaction not successful. Check your parameters/credentials';
$_['text_response_capture']         = 'Capture transaction completed successfully';
$_['text_response_refund']          = 'Refund transaction completed successfully';
$_['text_response_void']            = 'Void transaction completed successfully';

// Error
$_['error_permission']				= 'Warning: You do not have permission to modify payment module eMerchantPay!';
$_['error_username']				= 'Genesis Username is Required!';
$_['error_password']				= 'Genesis Password is Required!';
$_['error_token']					= 'Genesis Token is Required!';