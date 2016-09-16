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

if (!class_exists('ControllerPaymentEmerchantPayDirect')) {
    require_once DIR_APPLICATION . 'controller/payment/emerchantpay_direct.php';
}

/**
 * Front-end controller for the "eMerchantPay Direct" module (2.3.x and above)
 *
 * @package EMerchantPayDirect
 */
class ControllerExtensionPaymentEmerchantPayDirect extends ControllerPaymentEmerchantPayDirect
{

}