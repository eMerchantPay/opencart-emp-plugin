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

if (!class_exists('ControllerExtensionPaymentEmerchantPayBase')) {
	require_once DIR_APPLICATION . "controller/extension/payment/emerchantpay/base_controller.php";
}

/**
 * Backend controller for the "emerchantpay Direct" module
 *
 * @package EMerchantPayDirect
 */
class ControllerExtensionPaymentEmerchantPayDirect extends ControllerExtensionPaymentEmerchantPayBase
{
	/**
	 * Module Name (Used in View - Templates)
	 *
	 * @var string
	 */
	protected $module_name = 'emerchantpay_direct';

	/**
	 * Used to find out if the payment method requires SSL
	 *
	 * @return bool
	 */
	protected function isModuleRequiresSsl()
	{
		return true;
	}

	/**
	 * ControllerExtensionPaymentEmerchantPayDirect constructor.
	 * @param $registry
	 */
	public function __construct($registry)
	{
		parent::__construct($registry);
		array_push($this->error_field_key_list, 'token');
	}

	/**
	 * Ensure that the current user has permissions to see/modify this module
	 *
	 * @return bool
	 */
	protected function validate()
	{
		parent::validate();

		if (@empty($this->request->post["{$this->module_name}_token"])) {
			$this->error['token'] = $this->language->get('error_token');
		}

		return !$this->error;
	}
}
