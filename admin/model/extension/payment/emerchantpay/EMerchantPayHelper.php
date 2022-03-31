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
	const PPRO_TRANSACTION_SUFFIX     = '_ppro';
	const TRANSACTION_LANGUAGE_PREFIX = 'text_transaction_';

	const GOOGLE_PAY_TRANSACTION_PREFIX     = \Genesis\API\Constants\Transaction\Types::GOOGLE_PAY . '_';
	const GOOGLE_PAY_PAYMENT_TYPE_AUTHORIZE =
		\Genesis\API\Constants\Transaction\Parameters\Mobile\GooglePay\PaymentTypes::AUTHORIZE;
	const GOOGLE_PAY_PAYMENT_TYPE_SALE      =
		\Genesis\API\Constants\Transaction\Parameters\Mobile\GooglePay\PaymentTypes::SALE;

	const PAYPAL_TRANSACTION_PREFIX         = \Genesis\API\Constants\Transaction\Types::PAY_PAL . '_';
	const PAYPAL_PAYMENT_TYPE_AUTHORIZE     =
		\Genesis\API\Constants\Transaction\Parameters\Wallets\PayPal\PaymentTypes::AUTHORIZE;
	const PAYPAL_PAYMENT_TYPE_SALE          =
		\Genesis\API\Constants\Transaction\Parameters\Wallets\PayPal\PaymentTypes::SALE;
	const PAYPAL_PAYMENT_TYPE_EXPRESS       =
		\Genesis\API\Constants\Transaction\Parameters\Wallets\PayPal\PaymentTypes::EXPRESS;

	const APPLE_PAY_TRANSACTION_PREFIX      = \Genesis\API\Constants\Transaction\Types::APPLE_PAY . '_';
	const APPLE_PAY_PAYMENT_TYPE_AUTHORIZE  =
		\Genesis\API\Constants\Transaction\Parameters\Mobile\ApplePay\PaymentTypes::AUTHORIZE;
	const APPLE_PAY_PAYMENT_TYPE_SALE       =
		\Genesis\API\Constants\Transaction\Parameters\Mobile\ApplePay\PaymentTypes::SALE;

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
			\Genesis\API\Constants\Transaction\Types::INIT_RECURRING_SALE,
			\Genesis\API\Constants\Transaction\Types::INIT_RECURRING_SALE_3D
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

		foreach (\Genesis\API\Constants\Transaction\Types::getWPFTransactionTypes() as $type) {
			$key        = EMerchantPayHelper::TRANSACTION_LANGUAGE_PREFIX . $type;
			$data[$key] = \Genesis\API\Constants\Transaction\Names::getName($type);
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
	 * @return \Genesis\API\Request\Financial\Alternatives\Klarna\Items
	 * @throws \Genesis\Exceptions\ErrorParameter
	 */
	public static function getKlarnaCustomParamItems($order)
	{
		$tax_class_ids = self::getTaxClassIdFromProductInfo($order['additional']['product_info']);

		$items = new \Genesis\API\Request\Financial\Alternatives\Klarna\Items($order['currency']);
		foreach ($order['additional']['product_order_info'] as $product) {
			$tax_class_id = \Genesis\API\Request\Financial\Alternatives\Klarna\Item::ITEM_TYPE_PHYSICAL;
			if ($tax_class_ids[$product['product_id']] == ModelPaymentEmerchantPayBase::OC_TAX_CLASS_VIRTUAL_PRODUCT) {
				$tax_class_id = \Genesis\API\Request\Financial\Alternatives\Klarna\Item::ITEM_TYPE_DIGITAL;
			}

			$klarna_item = new \Genesis\API\Request\Financial\Alternatives\Klarna\Item(
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
				new \Genesis\API\Request\Financial\Alternatives\Klarna\Item(
					'Taxes',
					\Genesis\API\Request\Financial\Alternatives\Klarna\Item::ITEM_TYPE_SURCHARGE,
					1,
					$taxes
				)
			);
		}

		$shipping = floatval(self::getShippingFromOrderTotals($order['additional']['order_totals']));
		if ($shipping) {
			$items->addItem(
				new \Genesis\API\Request\Financial\Alternatives\Klarna\Item(
					'Shipping Costs',
					\Genesis\API\Request\Financial\Alternatives\Klarna\Item::ITEM_TYPE_SHIPPING_FEE,
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
}
