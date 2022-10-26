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
	require DIR_APPLICATION . '/../admin/model/extension/payment/emerchantpay/genesis/vendor/autoload.php';
}

use Genesis\API\Constants\Transaction\Parameters\Threeds\V2\CardHolderAccount\ShippingAddressUsageIndicators;
use Genesis\API\Constants\Transaction\Parameters\Threeds\V2\CardHolderAccount\RegistrationIndicators;
use Genesis\API\Constants\Transaction\Parameters\Threeds\V2\MerchantRisk\ReorderItemIndicators;
use Genesis\API\Constants\Transaction\Parameters\Threeds\V2\MerchantRisk\ShippingIndicators;
use Genesis\Utils\Common as CommonUtils;

/**
 * Threeds helper class
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class EMerchantPayThreedsHelper
{
	/**
	 * OpenCart DateTime format
	 */
	const OC_DATETIME_FORMAT = 'Y-m-d H:i:s';

	/**
	 * Indicator value constants
	 */
	const CURRENT_TRANSACTION_INDICATOR       = 'current_transaction';
	const LESS_THAN_30_DAYS_INDICATOR         = 'less_than_30_days';
	const MORE_THAN_30_LESS_THAN_60_INDICATOR = 'more_30_less_60_days';
	const MORE_THAN_60_DAYS_INDICATOR         = 'more_than_60_days';

	/**
	 * Activity periods
	 */
	const ACTIVITY_24_HOURS = 'PT24H';
	const ACTIVITY_6_MONTHS = 'P6M';
	const ACTIVITY_1_YEAR   = 'P1Y';

	/**
	 * @var array $complete_statuses Ids of statuses when order is completed successfully
	 */
	private static $complete_statuses = ['5', '3'];

	/**
	 * Check if cart contains physical product
	 *
	 * @param array $products
	 * @return bool
	 */
	public static function hasPhysicalProduct($products)
	{
		return in_array(
			ModelExtensionPaymentEmerchantPayBase::OC_TAX_CLASS_PHYSICAL_PRODUCT,
			array_column(
				$products,
				'tax_class_id'
			)
		);
	}

	/**
	 * Get shipping indicator
	 *
	 * @param bool  $has_physical_products
	 * @param array $order_info
	 * @param bool  $is_guest
	 *
	 * @return string
	 */
	public static function getShippingIndicator($has_physical_products, $order_info, $is_guest)
	{
		$indicator = ShippingIndicators::OTHER;

		if (!$has_physical_products) {
			return ShippingIndicators::DIGITAL_GOODS;
		}

		if (!$is_guest) {
			$indicator = ShippingIndicators::STORED_ADDRESS;

			if (self::areAddressesSame($order_info)) {
				$indicator = ShippingIndicators::SAME_AS_BILLING;
			}
		}

		return $indicator;
	}

	/**
	 * Get Reorder indicator
	 *
	 * @param Model $model
	 * @param bool  $is_quest
	 * @param array $product_info
	 *
	 * @return string
	 */
	public static function getReorderItemsIndicator($model, $is_quest, $product_info, $customer_orders)
	{
		if ($is_quest) {
			return ReorderItemIndicators::FIRST_TIME;
		}

		$bought_product_ids = self::getBoughtProducts($model, $customer_orders);
		$product_ids        = array_column($product_info, 'product_id');

		foreach ($product_ids as $product_id) {
			if (in_array($product_id, $bought_product_ids)) {
				return ReorderItemIndicators::REORDERED;
			}
		}

		return ReorderItemIndicators::FIRST_TIME;
	}

	/**
	 * Get Shipping indicator
	 *
	 * @param string $date
	 *
	 * @return string
	 */
	public static function getShippingAddressUsageIndicator($date)
	{
		return self::getIndicatorValue($date, ShippingAddressUsageIndicators::class);
	}

	/**
	 * Get Registration indicator
	 *
	 * @param string $order_date
	 *
	 * @return string
	 */
	public static function getRegistrationIndicator($order_date)
	{
		return self::getIndicatorValue($order_date, RegistrationIndicators::class);
	}

	/**
	 * Find the date when customer placed first order
	 *
	 * @param Model $model
	 *
	 * @return string
	 */
	public static function findFirstCustomerOrderDate($customer_orders)
	{
		$order_date = (new DateTime())->format(self::OC_DATETIME_FORMAT);

		if (CommonUtils::isValidArray($customer_orders)) {
			$order_date = $customer_orders[0]['date_added'];
		}

		return $order_date;
	}

	/**
	 * Date when the customer has been registered
	 *
	 * @param Model  $model
	 * @param string $customer_id
	 *
	 * @return string
	 */
	public static function getCreationDate($model, $customer_id)
	{
		$customer = $model->getCustomer($customer_id);

		return $customer['date_added'];
	}

	/**
	 * Finds the date when current shipping address has been used for the first time
	 *
	 * @param Model $model
	 * @param array $order_info
	 *
	 * @return string
	 */
	public static function findShippingAddressDateFirstUsed($model, $order_info, $customer_orders)
	{
		$cart_shipping_address = [
			$order_info['shipping_firstname'],
			$order_info['shipping_lastname'],
			$order_info['shipping_address_1'],
			$order_info['shipping_address_2'],
			$order_info['shipping_postcode'],
			$order_info['shipping_city'],
			$order_info['shipping_zone_code'],
			$order_info['shipping_country_id'],
		];

		if (CommonUtils::isValidArray($customer_orders)) {
			foreach ($customer_orders as $customer_order) {
				$order = $model->getOrder($customer_order['order_id']);
				$order_shipping_address = [
					$order['shipping_firstname'],
					$order['shipping_lastname'],
					$order['shipping_address_1'],
					$order['shipping_address_2'],
					$order['shipping_postcode'],
					$order['shipping_city'],
					$order['shipping_zone_code'],
					$order['shipping_country_id'],
				];

				if (count(array_diff($cart_shipping_address, $order_shipping_address)) === 0) {
					return $customer_order['date_added'];
				}
			}
		}

		return (new DateTime())->format(self::OC_DATETIME_FORMAT);
	}

	/**
	 * How many orders has been placed for a given period
	 *
	 * @param Model $model
	 *
	 * @return array
	 *
	 * @throws Exception
	 */
	public static function findNumberOfOrdersForaPeriod($model, $customer_orders)
	{
		$customer_orders      = array_reverse($customer_orders);
		$start_date_last_24h  = (new DateTime())->sub(new DateInterval(self::ACTIVITY_24_HOURS));
		$start_date_last_6m   = (new DateTime())->sub(new DateInterval(self::ACTIVITY_6_MONTHS));
		$start_date_last_year = (new DateTime())->sub(new DateInterval(self::ACTIVITY_1_YEAR));

		$number_of_orders_last_24h  = 0;
		$number_of_orders_last_6m   = 0;
		$number_of_orders_last_year = 0;

		if (CommonUtils::isValidArray($customer_orders)) {
			foreach ($customer_orders as $customer_order) {
				$order_date = DateTime::createFromFormat(
					self::OC_DATETIME_FORMAT,
					$customer_order['date_added']
				);

				// We don't need orders older than a year
				if ($order_date < $start_date_last_year) {
					break;
				}

				// Get order details only if the order was placed within the last 6 months
				if ($order_date >= $start_date_last_6m) {
					$order = $model->getOrder($customer_order['order_id']);

					// Check if the order status is complete or shipped
					$number_of_orders_last_6m += (in_array($order['order_status_id'], self::$complete_statuses)) ? 1 : 0;
				}

				$number_of_orders_last_24h += ($order_date >= $start_date_last_24h) ? 1 : 0;
				$number_of_orders_last_year++;
			}
		}

		return [
			'last_24h'  => $number_of_orders_last_24h,
			'last_6m'   => $number_of_orders_last_6m,
			'last_year' => $number_of_orders_last_year
		];
	}

	/**
	 * Compare billing and shipping addresses
	 *
	 * @param array $order_info
	 *
	 * @return bool
	 */
	private static function areAddressesSame($order_info)
	{
		$shipping = [
			$order_info['shipping_firstname'],
			$order_info['shipping_lastname'],
			$order_info['shipping_address_1'],
			$order_info['shipping_address_2'],
			$order_info['shipping_postcode'],
			$order_info['shipping_city'],
			$order_info['shipping_zone_code'],
			$order_info['shipping_iso_code_2'],
		];

		$billing = [
			$order_info['payment_firstname'],
			$order_info['payment_lastname'],
			$order_info['payment_address_1'],
			$order_info['payment_address_2'],
			$order_info['payment_postcode'],
			$order_info['payment_city'],
			$order_info['payment_zone_code'],
			$order_info['payment_iso_code_2'],
		];

		return count(array_diff($shipping, $billing) ) === 0;
	}

	/**
	 * Returns distinct array of all bought products by this customer
	 *
	 * @param Model $model
	 * @param array $customer_orders
	 *
	 * @return array
	 */
	private static function getBoughtProducts($model, $customer_orders)
	{
		$bought_products = [];
		$order_ids       = array_column($customer_orders, 'order_id');

		foreach ($order_ids as $order_id) {
			$order_products  = $model->getOrderProducts($order_id);
			$bought_products = array_merge($bought_products, array_column($order_products, 'product_id'));
		}

		return $bought_products;
	}

	/**
	 * Get indicator value according the given period of time
	 *
	 * @param string $date
	 * @param string $indicator_class
	 *
	 * @return string
	 */
	private static function getIndicatorValue($date, $indicator_class)
	{
		switch (self::getDateIndicator($date)) {
			case static::LESS_THAN_30_DAYS_INDICATOR:
				return $indicator_class::LESS_THAN_30DAYS;
			case static::MORE_THAN_30_LESS_THAN_60_INDICATOR:
				return $indicator_class::FROM_30_TO_60_DAYS;
			case static::MORE_THAN_60_DAYS_INDICATOR:
				return $indicator_class::MORE_THAN_60DAYS;
			default:
				return $indicator_class::CURRENT_TRANSACTION;
		}
	}

	/**
	 * Check if date is less than 30, between 30 and 60 or more than 60 days
	 *
	 * @param string $date
	 *
	 * @return string
	 */
	private static function getDateIndicator($date)
	{
		$now        = new DateTime();
		$check_date = DateTime::createFromFormat(self::OC_DATETIME_FORMAT, $date);
		$days       = $check_date->diff($now)->days;

		if ($days < 1) {
			return self::CURRENT_TRANSACTION_INDICATOR;
		}
		if ($days < 30) {
			return self::LESS_THAN_30_DAYS_INDICATOR;
		}
		if ($days < 60) {
			return self::MORE_THAN_30_LESS_THAN_60_INDICATOR;
		}

		return self::MORE_THAN_60_DAYS_INDICATOR;
	}
}
