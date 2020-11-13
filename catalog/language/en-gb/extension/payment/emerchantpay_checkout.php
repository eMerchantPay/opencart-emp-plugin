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

// Text
$_['text_title']                  = 'Credit Card / Debit Card (emerchantpay Checkout)';
$_['text_trial_single_payment']   = '%s for the first %s %s, then ';
$_['text_trial_multiple_payment'] = '%s every %s %s for %s payments, then ';
$_['text_recurring']              = '%s every %s %s';
$_['text_length']                 = ' for %s payments';

// Buttons
$_['button_shopping_cart'] = 'Go to shopping cart';

// Warnings
$_['text_payment_mixed_cart_content'] = 'Your order cannot be processed with the emerchantpay Checkout payment method with the selected shopping cart content. '
	. 'Recurring items cannot be ordered along with other recurring or non-recurring items. '
	. 'Please split your order on multiple orders placing recurring item(s) separately (one per order).';

// Errors
$_['text_payment_failure']            = 'The payment attempt was unsuccessful, please verify your input and/or try again later!';
$_['text_payment_system_error']       = "Sorry, we're experiencing issues processing your order.\nPlease try again or contact us for assistance!";
$_['text_payment_cancelled']          = 'Your checkout session has been successfully cancelled!';

// Order Status
$_['text_payment_status_initiated']    = 'Checkout session initiated...';
$_['text_payment_status_init_failed']  = 'Checkout session initialization failed!';
$_['text_payment_status_successful']   = 'Checkout session successfully completed!';
$_['text_payment_status_unsuccessful'] = 'Checkout session failed!';
