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

use Genesis\Api\Request\Financial\Alternatives\Klarna\Item;
use Genesis\Api\Request\Financial\Alternatives\Klarna\Items;

if (!class_exists('\Genesis\Genesis', false)) {
	if (strpos(DIR_APPLICATION, 'admin') === false) {
		$path = DIR_APPLICATION . '/../admin/model/extension/payment/emerchantpay/genesis/vendor/autoload.php';
	} else {
		$path = DIR_APPLICATION . 'model/extension/payment/emerchantpay/genesis/vendor/autoload.php';
	}

	require $path;
}

if (!class_exists('ModelExtensionPaymentEmerchantPayBase', false)) {
	if (strpos(DIR_APPLICATION, 'admin') === false) {
		$path = DIR_APPLICATION . 'model/extension/payment/emerchantpay/base_model.php';
	} else {
		$path = DIR_CATALOG . 'model/extension/payment/emerchantpay/base_model.php';
	}

	require $path;
}

/**
 * Class EMerchantPayHelper
 * @package EMerchantPay
 */
class EMerchantPayHelper
{
	const TRANSACTION_LANGUAGE_PREFIX = 'text_transaction_';

	const GOOGLE_PAY_TRANSACTION_PREFIX     = \Genesis\Api\Constants\Transaction\Types::GOOGLE_PAY . '_';
	const GOOGLE_PAY_PAYMENT_TYPE_AUTHORIZE =
		\Genesis\Api\Constants\Transaction\Parameters\Mobile\GooglePay\PaymentTypes::AUTHORIZE;
	const GOOGLE_PAY_PAYMENT_TYPE_SALE      =
		\Genesis\Api\Constants\Transaction\Parameters\Mobile\GooglePay\PaymentTypes::SALE;

	const PAYPAL_TRANSACTION_PREFIX         = \Genesis\Api\Constants\Transaction\Types::PAY_PAL . '_';
	const PAYPAL_PAYMENT_TYPE_AUTHORIZE     =
		\Genesis\Api\Constants\Transaction\Parameters\Wallets\PayPal\PaymentTypes::AUTHORIZE;
	const PAYPAL_PAYMENT_TYPE_SALE          =
		\Genesis\Api\Constants\Transaction\Parameters\Wallets\PayPal\PaymentTypes::SALE;
	const PAYPAL_PAYMENT_TYPE_EXPRESS       =
		\Genesis\Api\Constants\Transaction\Parameters\Wallets\PayPal\PaymentTypes::EXPRESS;

	const APPLE_PAY_TRANSACTION_PREFIX      = \Genesis\Api\Constants\Transaction\Types::APPLE_PAY . '_';
	const APPLE_PAY_PAYMENT_TYPE_AUTHORIZE  =
		\Genesis\Api\Constants\Transaction\Parameters\Mobile\ApplePay\PaymentTypes::AUTHORIZE;
	const APPLE_PAY_PAYMENT_TYPE_SALE       =
		\Genesis\Api\Constants\Transaction\Parameters\Mobile\ApplePay\PaymentTypes::SALE;

	const REFERENCE_ACTION_CAPTURE = 'capture';
	const REFERENCE_ACTION_REFUND  = 'refund';

	/**
	 * Retrieve Recurring Transaction Types
	 *
	 * @return array
	 */
	public static function getRecurringTransactionTypes()
	{
		return array(
			\Genesis\Api\Constants\Transaction\Types::INIT_RECURRING_SALE,
			\Genesis\Api\Constants\Transaction\Types::INIT_RECURRING_SALE_3D
		);
	}

	/**
	 * Retrieve common Transaction Type Names
	 *
	 * @return array
	 */
	public static function getTransactionTypeNames()
	{
		$data = array();

		foreach (\Genesis\Api\Constants\Transaction\Types::getWPFTransactionTypes() as $type) {
			$key        = EMerchantPayHelper::TRANSACTION_LANGUAGE_PREFIX . $type;
			$data[$key] = \Genesis\Api\Constants\Transaction\Names::getName($type);
		}

		return $data;
	}

	/**
	 * Create Klarna Authorize Items
	 *
	 * @param $order
	 *      Array array (
	 *          currency =>
	 *          additional => array (
	 *              product_info => array
	 *              order_total  => array
	 *          )
	 *      )
	 *
	 * @return \Genesis\Api\Request\Financial\Alternatives\Klarna\Items
	 * @throws \Genesis\Exceptions\ErrorParameter
	 */
	public static function getKlarnaCustomParamItems($order)
	{
		$tax_class_ids = self::getTaxClassIdFromProductInfo($order['additional']['product_info']);

		$items = new Items($order['currency']);
		foreach ($order['additional']['product_order_info'] as $product) {
			$tax_class_id = \Genesis\Api\Request\Financial\Alternatives\Klarna\Item::ITEM_TYPE_PHYSICAL;
			if ($tax_class_ids[$product['product_id']] == ModelPaymentEmerchantPayBase::OC_TAX_CLASS_VIRTUAL_PRODUCT) {
				$tax_class_id = \Genesis\Api\Request\Financial\Alternatives\Klarna\Item::ITEM_TYPE_DIGITAL;
			}

			$klarna_item = new Item(
				$product['name'],
				$tax_class_id,
				$product['quantity'],
				$product['price']
			);
			$items->addItem($klarna_item);

		}

		$taxes = floatval(self::getTaxFromOrderTotals($order['additional']['order_totals']));
		if ($taxes) {
			$items->addItem(
				new Item(
					'Taxes',
					\Genesis\Api\Request\Financial\Alternatives\Klarna\Item::ITEM_TYPE_SURCHARGE,
					1,
					$taxes
				)
			);
		}

		$shipping = floatval(self::getShippingFromOrderTotals($order['additional']['order_totals']));
		if ($shipping) {
			$items->addItem(
				new Item(
					'Shipping Costs',
					\Genesis\Api\Request\Financial\Alternatives\Klarna\Item::ITEM_TYPE_SHIPPING_FEE,
					1,
					$shipping
				)
			);
		}

		return $items;
	}

	/**
	 * Extract TaxClassId from ProductInfo
	 *      Returns Array (product_id => tax_class_id)
	 * @param array $products
	 * @return array
	 */
	public static function getTaxClassIdFromProductInfo($products)
	{
		$class_ids = array();

		foreach($products as $product) {
			$class_ids[$product['product_id']] = $product['tax_class_id'];
		}

		return $class_ids;
	}

	/**
	 * Calculate the Shipping cost from Order Total
	 * @param $order_totals
	 * @return int
	 */
	public static function getShippingFromOrderTotals($order_totals) {
		$shipping = 0;

		foreach($order_totals as $item_total) {
			if ($item_total['code'] == 'shipping') {
				$shipping += $item_total['value'];
			}
		}

		return $shipping;
	}

	/**
	 * Calculate the Taxes const from Order Total
	 * @param $order_totals
	 * @return int
	 */
	public static function getTaxFromOrderTotals($order_totals) {
		$tax = 0;

		foreach($order_totals as $item_total) {
			if ($item_total['code'] == 'tax') {
				$tax += $item_total['value'];
			}
		}

		return $tax;
	}

	/**
	 * Return list of available Bank Codes for Online banking
	 *
	 * @return array
	 */
	public static function getAvailableBankCodes() {
		return [
			\Genesis\Api\Constants\Banks::CPI => 'Interac Combined Pay-in',
			\Genesis\Api\Constants\Banks::BCT => 'Bancontact',
			\Genesis\Api\Constants\Banks::BLK => 'BLIK',
			\Genesis\Api\Constants\Banks::SE  => 'SPEI',
			\Genesis\Api\Constants\Banks::PID => 'LatiPay'
		];
	}

	/**
	 * Sanitize data before insert into DB
	 * @param $data
	 * @param $model
	 * @return array
	 */
	public static function sanitizeData($data, $model)
	{
		$result = array();

		array_walk($data, function ($value, $key) use ($model, &$result) {
			$result[$model->db->escape($key)] = $model->db->escape($value);
		});

		return $result;
	}
}
