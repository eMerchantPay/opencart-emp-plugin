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

require_once DIR_APPLICATION . 'model/extension/payment/emerchantpay/base_model.php';

use Genesis\Api\Constants\Transaction\Types;
use Genesis\Genesis;
use Genesis\Api\Constants\Transaction\States;

/**
 * Front-end model for the "emerchantpay Checkout" module
 *
 * @package EMerchantPayCheckout
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.LongClassName)
 */
class ModelExtensionPaymentEmerchantPayCheckout extends ModelExtensionPaymentEmerchantPayBase
{
	/**
	 * Module Name
	 *
	 * @var string
	 */
	protected $module_name = 'emerchantpay_checkout';

	/**
	 * Main method
	 *
	 * @param $address Order Address
	 * @param $total   Order Total
	 *
	 * @return array
	 */
	public function getMethod($address, $total)
	{
		$this->load->language('extension/payment/emerchantpay_checkout');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('emerchantpay_checkout_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if ($this->config->get('emerchantpay_checkout_total') > 0 && $this->config->get('emerchantpay_checkout_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('emerchantpay_checkout_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		$method_data = array();

		if ($status) {
			$method_data = array(
				'code'       => 'emerchantpay_checkout',
				'title'      => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('emerchantpay_checkout_sort_order')
			);
		}

		return $method_data;
	}

	/**
	 * @param $email
	 *
	 * @return null|string
	 */
	public function getConsumerId($email)
	{
		$query = $this->db->query("
			SELECT * FROM
				`" . DB_PREFIX . "emerchantpay_checkout_consumers`
			WHERE
				`customer_email` = '" . $this->db->escape($email) . "' LIMIT 1
		");

		if ($query->num_rows) {
			return $query->rows[0]['consumer_id'];
		}

		return $this->retrieveConsumerIdFromGenesisGateway($email);
	}

	/**
	 * @param string $email
	 *
	 * @return null|string
	 */
	protected function retrieveConsumerIdFromGenesisGateway($email)
	{
		try {
			$genesis = new Genesis('NonFinancial\Consumers\Retrieve');
			$genesis->request()->setEmail($email);

			$genesis->execute();

			$response = $genesis->response()->getResponseObject();

			if ($this->isErrorResponse($response)) {
				return null;
			}

			return $response->consumer_id;
		} catch (\Exception $exception) {
			return null;
		}
	}

	/**
	 * @param $response
	 *
	 * @return bool
	 */
	protected function isErrorResponse($response)
	{
		$state = new States($response->status);

		return $state->isError();
	}

	/**
	 * @param $email
	 * @param $consumer_id
	 */
	public function addConsumer($email, $consumer_id)
	{
		try {
			$query = $this->db->query("
				SELECT * FROM
					`" . DB_PREFIX . "emerchantpay_checkout_consumers`
				WHERE
					`customer_email` = '" . $this->db->escape($email) . "' LIMIT 1
			");

			if ($query->num_rows) {
				return ;
			}

			$this->db->query("
				INSERT INTO
					`" . DB_PREFIX . "emerchantpay_checkout_consumers` (`customer_email`, `consumer_id`)
				VALUES
					('" . $this->db->escape($email) . "', '" . $this->db->escape($consumer_id) . "')
			");
		} catch (\Exception $exception) {
			$this->logEx($exception);
		}
	}

	/**
	 * Get saved transaction (from DB) by id
	 *
	 * @param $unique_id
	 *
	 * @return bool|mixed
	 */
	public function getTransactionById($unique_id)
	{
		if (isset($unique_id) && !empty($unique_id)) {
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "emerchantpay_checkout_transactions` WHERE `unique_id` = '" . $this->db->escape($unique_id) . "' LIMIT 1");

			if ($query->num_rows) {
				return reset($query->rows);
			}
		}

		return false;
	}

	/**
	 * Add transaction to the database
	 *
	 * @param $data array
	 */
	public function addTransaction($data)
	{
		try {
			$fields = implode(', ', array_map(
					function ($value, $key) {
						return sprintf('`%s`', $key);
					},
					$data,
					array_keys($data)
				)
			);

			$values = implode(', ', array_map(
					function ($value) {
						return sprintf("'%s'", $value);
					},
					$data,
					array_keys($data)
				)
			);

			$this->db->query("
				INSERT INTO
					`" . DB_PREFIX . "emerchantpay_checkout_transactions` (" . $fields . ")
				VALUES
					(" . $values . ")
			");
		} catch (Exception $exception) {
			$this->logEx($exception);
		}
	}

	/**
	 * Update existing transaction in the database
	 *
	 * @param $data array
	 */
	public function updateTransaction($data)
	{
		try {
			$fields = implode(', ', array_map(
					function ($value, $key) {
						return sprintf("`%s` = '%s'", $key, $value);
					},
					$data,
					array_keys($data)
				)
			);

			$this->db->query("
				UPDATE
					`" . DB_PREFIX . "emerchantpay_checkout_transactions`
				SET
					" . $fields . "
				WHERE
				    `unique_id` = '" . $data['unique_id'] . "'
			");
		} catch (Exception $exception) {
			$this->logEx($exception);
		}
	}

	/**
	 * Sanitize transaction data and check
	 * whether an UPDATE or INSERT is required
	 *
	 * @param array $data
	 */
	public function populateTransaction($data = array())
	{
		try {
			$data = EMerchantPayHelper::sanitizeData($data, $this);

			// Check if transaction exists
			$insert_query = $this->db->query("
				SELECT
					*
				FROM
					`" . DB_PREFIX . "emerchantpay_checkout_transactions`
				WHERE
					`unique_id` = '" . $data['unique_id'] . "'
			");

			if ($insert_query->rows) {
				$this->updateTransaction($data);
			} else {
				$this->addTransaction($data);
			}
		} catch (Exception $exception) {
			$this->logEx($exception);
		}
	}

	/**
	 * Send transaction to Genesis
	 *
	 * @param $data array Transaction Data
	 *
	 * @return \Genesis\Api\Response
	 *
	 * @throws Exception
	 */
	public function create($data)
	{
		try {
			$this->bootstrap();

			$genesis = new Genesis('Wpf\Create');

			$genesis
				->request()
				->setTransactionId($data['transaction_id'])
				// Financial
				->setCurrency($data['currency'])
				->setAmount($data['amount'])
				->setUsage($data['usage'])
				->setDescription($data['description'])
				// Personal
				->setCustomerEmail($data['customer_email'])
				->setCustomerPhone($data['customer_phone'])
				// URL
				->setNotificationUrl($data['notification_url'])
				->setReturnSuccessUrl($data['return_success_url'])
				->setReturnFailureUrl($data['return_failure_url'])
				->setReturnCancelUrl($data['return_cancel_url'])
				->setReturnPendingUrl($data['return_success_url'])
				// Billing
				->setBillingFirstName($data['billing']['first_name'])
				->setBillingLastName($data['billing']['last_name'])
				->setBillingAddress1($data['billing']['address1'])
				->setBillingAddress2($data['billing']['address2'])
				->setBillingZipCode($data['billing']['zip'])
				->setBillingCity($data['billing']['city'])
				->setBillingState($data['billing']['state'])
				->setBillingCountry($data['billing']['country'])
				// Shipping
				->setShippingFirstName($data['shipping']['first_name'])
				->setShippingLastName($data['shipping']['last_name'])
				->setShippingAddress1($data['shipping']['address1'])
				->setShippingAddress2($data['shipping']['address2'])
				->setShippingZipCode($data['shipping']['zip'])
				->setShippingCity($data['shipping']['city'])
				->setShippingState($data['shipping']['state'])
				->setShippingCountry($data['shipping']['country'])
				->setLanguage($data['language']);

			$this->addTransactionTypesToGatewayRequest($genesis, $data);

			if ($this->isWpfTokenizationEnabled()) {
				$this->prepareWpfRequestTokenization($genesis);
			}

			if ($this->isThreedsAllowed()) {
				/** @var \Genesis\Api\Request\Wpf\Create $request */
				$request = $genesis->request();
				$request->setThreedsV2ControlChallengeIndicator($data['threeds_challenge_indicator'])
					->setThreedsV2PurchaseCategory($data['threeds_purchase_category'])
					->setThreedsV2MerchantRiskDeliveryTimeframe($data['threeds_delivery_timeframe'])
					->setThreedsV2MerchantRiskShippingIndicator($data['threeds_shipping_indicator'])
					->setThreedsV2MerchantRiskReorderItemsIndicator($data['threeds_reorder_items_indicator'])
					->setThreedsV2CardHolderAccountRegistrationDate($data['threeds_registration_date'])
					->setThreedsV2CardHolderAccountRegistrationIndicator($data['threeds_registration_indicator'])
				;
				if (!$data['is_guest']) {
					$request->setThreedsV2CardHolderAccountCreationDate($data['threeds_creation_date'])
						->setThreedsV2CardHolderAccountShippingAddressDateFirstUsed($data['threads_shipping_address_date_first_used'])
						->setThreedsV2CardHolderAccountShippingAddressUsageIndicator($data['threeds_shipping_address_usage_indicator'])
						->setThreedsV2CardHolderAccountTransactionsActivityLast24Hours($data['transactions_activity_last_24_hours'])
						->setThreedsV2CardHolderAccountTransactionsActivityPreviousYear($data['transactions_activity_previous_year'])
						->setThreedsV2CardHolderAccountPurchasesCountLast6Months($data['purchases_count_last_6_months'])
					;
				}
			}

			$wpf_amount = (float)$genesis->request()->getAmount();
			if ($wpf_amount <= $data['sca_exemption_amount']) {
				$genesis->request()->setScaExemption($data['sca_exemption_value']);
			}

			$genesis->execute();

			$this->saveWpfTokenizationData($genesis);

			return $genesis->response();
		} catch (\Exception $exception) {
			$this->logEx($exception);

			throw $exception;
		}
	}

	/**
	 * @param \Genesis\Genesis $genesis
	 */
	protected function prepareWpfRequestTokenization(\Genesis\Genesis $genesis)
	{
		$genesis->request()->setRememberCard(true);

		$consumer_id = $this->getConsumerId($genesis->request()->getCustomerEmail());

		if ($consumer_id) {
			$genesis->request()->setConsumerId($consumer_id);
		}
	}

	/**
	 * @return bool
	 */
	protected function isWpfTokenizationEnabled()
	{
		return (bool)$this->config->get('emerchantpay_checkout_wpf_tokenization');
	}

	protected function isThreedsAllowed()
	{
		return (bool)$this->config->get('emerchantpay_checkout_threeds_allowed');
	}

	/**
	 * @param $genesis
	 */
	protected function saveWpfTokenizationData($genesis)
	{
		if (!empty($genesis->response()->getResponseObject()->consumer_id)) {
			$this->addConsumer(
				$genesis->request()->getCustomerEmail(),
				$genesis->response()->getResponseObject()->consumer_id
			);
		}
	}

	/**
	 * Genesis Request - Reconcile
	 *
	 * @param $unique_id string - Id of a Genesis Transaction
	 * @return mixed
	 * @throws Exception
	 */
	public function reconcile($unique_id)
	{
		try {
			$this->bootstrap();

			$genesis = new Genesis('Wpf\Reconcile');

			$genesis->request()->setUniqueId($unique_id);

			$genesis->execute();

			return $genesis->response()->getResponseObject();
		} catch (\Exception $exception) {
			$this->logEx($exception);

			return false;
		}
	}

	/**
	 * Bootstrap Genesis Library
	 *
	 * @return void
	 */
	public function bootstrap()
	{
		// Look for, but DO NOT try to load via Auto-loader magic methods
		if (!class_exists('\Genesis\Genesis', false)) {
			include DIR_APPLICATION . '/../admin/model/extension/payment/emerchantpay/genesis/vendor/autoload.php';

			\Genesis\Config::setEndpoint(
				\Genesis\Api\Constants\Endpoints::EMERCHANTPAY
			);

			\Genesis\Config::setUsername(
				$this->config->get('emerchantpay_checkout_username')
			);

			\Genesis\Config::setPassword(
				$this->config->get('emerchantpay_checkout_password')
			);

			\Genesis\Config::setEnvironment(
				$this->config->get('emerchantpay_checkout_sandbox') ? \Genesis\Api\Constants\Environments::STAGING : \Genesis\Api\Constants\Environments::PRODUCTION
			);
		}
	}

	/**
	 * Generate Transaction Id based on the order id
	 * and salted to avoid duplication
	 *
	 * @param string $prefix
	 *
	 * @return string
	 */
	public function genTransactionId($prefix = '')
	{
		$hash = md5(microtime(true) . uniqid() . mt_rand(PHP_INT_SIZE, PHP_INT_MAX));

		return (string)$prefix . substr($hash, -(strlen($hash) - strlen($prefix)));
	}

	/**
	 * Get the current front-end language
	 *
	 * @return string
	 */
	public function getLanguage()
	{
		$language = isset($this->session->data['language']) ? $this->session->data['language'] : $this->config->get('config_language');

		$language_code = substr($language, 0, 2);

		$this->bootstrap();

		if (defined('\Genesis\Api\Constants\i18n::' . strtoupper($language_code))) {
			return strtolower($language_code);
		} else {
			return 'en';
		}
	}

	/**
	 * Get a description-formatted list of products
	 * inside an order
	 *
	 * @param $order_id
	 * @return string
	 */
	public function getOrderProducts($order_id)
	{
		$order_product_query = $this->db->query("
            SELECT
                *
            FROM
                " . DB_PREFIX . "order_product
            WHERE
                order_id = '" . abs((int)$order_id) . "'
            ");

		$description = '';

		foreach ($order_product_query->rows as $order_product) {
			$description .= sprintf("%s (%s) x %d\r\n", $order_product['name'], $order_product['model'], $order_product['quantity']);
		}

		return $description;
	}

	/**
	 * Get the Order Products stored in the Database
	 *
	 * @param $order_id
	 * @return mixed
	 */
	public function getDbOrderProducts($order_id) {
		$query = $this->db->query("
			SELECT
				*
			FROM " . DB_PREFIX . "order_product
			WHERE
				order_id = '" . (int)$order_id . "'
			");

		return $query->rows;
	}

	/**
	 * Get the Order Totals stored in the Database
	 *
	 * @param $order_id
	 * @return mixed
	 */
	public function getOrderTotals($order_id) {
		$query = $this->db->query("
			SELECT
				*
			FROM " . DB_PREFIX . "order_total
			WHERE
				order_id = '" . (int)$order_id . "' ORDER BY sort_order
			");

		return $query->rows;
	}

	/**
	 * Get Products Information
	 *
	 * @param array $products
	 * @return mixed
	 */
	public function getProductsInfo($products = array())
	{
		$ids = array();
		foreach ($products as $product) {
			array_push($ids, abs((int)$product));
		}

		$products_resource = $this->db->query("
			SELECT
				*
			FROM
				" . DB_PREFIX . "product
			WHERE
				product_id IN (" . implode(', ', $ids) . ")
		");

		return $products_resource->rows;
	}

	/**
	 * Get the selected transaction types in array
	 *
	 * @return array
	 */
	public function getTransactionTypes()
	{
		$processed_list = array();
		$alias_map      = array();

		$selected_types = $this->orderCardTransactionTypes(
			$this->config->get('emerchantpay_checkout_transaction_type')
		);

		$alias_map = [
			self::GOOGLE_PAY_TRANSACTION_PREFIX . self::GOOGLE_PAY_PAYMENT_TYPE_AUTHORIZE =>
				Types::GOOGLE_PAY,
			self::GOOGLE_PAY_TRANSACTION_PREFIX . self::GOOGLE_PAY_PAYMENT_TYPE_SALE      =>
				Types::GOOGLE_PAY,
			self::PAYPAL_TRANSACTION_PREFIX . self::PAYPAL_PAYMENT_TYPE_AUTHORIZE         =>
				Types::PAY_PAL,
			self::PAYPAL_TRANSACTION_PREFIX . self::PAYPAL_PAYMENT_TYPE_SALE              =>
				Types::PAY_PAL,
			self::PAYPAL_TRANSACTION_PREFIX . self::PAYPAL_PAYMENT_TYPE_EXPRESS           =>
				Types::PAY_PAL,
			self::APPLE_PAY_TRANSACTION_PREFIX . self::APPLE_PAY_PAYMENT_TYPE_AUTHORIZE   =>
				Types::APPLE_PAY,
			self::APPLE_PAY_TRANSACTION_PREFIX . self::APPLE_PAY_PAYMENT_TYPE_SALE        =>
				Types::APPLE_PAY,
		];

		foreach ($selected_types as $selected_type) {
			if (array_key_exists($selected_type, $alias_map)) {
				$transaction_type = $alias_map[$selected_type];

				$processed_list[$transaction_type]['name'] = $transaction_type;

				// WPF Custom Attribute
				$key = $this->getCustomParameterKey($transaction_type);

				$processed_list[$transaction_type]['parameters'][] = array(
					$key => str_replace(
						[
							self::GOOGLE_PAY_TRANSACTION_PREFIX,
							self::PAYPAL_TRANSACTION_PREFIX,
							self::APPLE_PAY_TRANSACTION_PREFIX
						],
						'',
						$selected_type
					)
				);
			} else {
				$processed_list[] = $selected_type;
			}
		}

		return $processed_list;
	}

	/**
	 * @param \Genesis\Genesis $genesis
	 * @param $order
	 * @throws \Genesis\Exceptions\ErrorParameter
	 */
	public function addTransactionTypesToGatewayRequest(\Genesis\Genesis $genesis, $order)
	{
		$types = $this->isRecurringOrder() ? $this->getRecurringTransactionTypes() : $this->getTransactionTypes();

		foreach ($types as $type) {
			if (is_array($type)) {
				$genesis
					->request()
					->addTransactionType($type['name'], $type['parameters']);

				continue;
			}

			$parameters = $this->getCustomRequiredAttributes($type, $order);

			if (!isset($parameters)) {
				$parameters = array();
			}

			$genesis
				->request()
				->addTransactionType(
					$type,
					$parameters
				);
			unset($parameters);
		}
	}

	/**
	 * @param string $type Transaction Type
	 * @param array $order Transformed Order Array
	 * @return array
	 * @throws \Genesis\Exceptions\ErrorParameter
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 */
	public function getCustomRequiredAttributes($type, $order)
	{
		$parameters = array();
		switch ($type) {
			case Types::IDEBIT_PAYIN:
			case Types::INSTA_DEBIT_PAYIN:
				$parameters = array(
					'customer_account_id' => $order['additional']['user_hash']
				);
				break;
			case Types::INVOICE:
				$parameters = EMerchantPayHelper::getInvoiceCustomParamItems($order)->toArray();
				break;
			case Types::TRUSTLY_SALE:
				$current_user_id = $order['additional']['user_id'];
				$user_id         = ($current_user_id > 0) ? $current_user_id : $order['additional']['user_hash'];
				$parameters = array(
					'user_id' => $user_id
				);
				break;
			case Types::ONLINE_BANKING_PAYIN:
				$selected_bank_codes = $this->config->get('emerchantpay_checkout_bank_codes');

				if (\Genesis\Utils\Common::isValidArray($selected_bank_codes)) {
					$parameters['bank_codes'] = array_map(
						function ($value) {
							return ['bank_code' => $value];
						},
						$selected_bank_codes
					);
				}
				break;
			case Types::PAYSAFECARD:
				$current_user_id = $order['additional']['user_id'];
				$customer_id     = ($current_user_id > 0) ? $current_user_id : $order['additional']['user_hash'];
				$parameters      = array(
					'customer_id' => $customer_id
				);
			    break;
		}

		return $parameters;
	}

	/**
	 * Get the selected transaction types in array
	 *
	 * @return array
	 */
	public function getRecurringTransactionTypes()
	{
		return $this->config->get('emerchantpay_checkout_recurring_transaction_type');
	}

	/**
	 * Get a Usage string with the Store Name
	 *
	 * @return string
	 */
	public function getUsage()
	{
		return sprintf('%s checkout transaction', $this->config->get('config_name'));
	}

	/**
	 * Retrieve the current logged user ID
	 *
	 * @return int
	 */
	public function getCurrentUserId()
	{
		return array_key_exists('user_id', $this->session->data) ? $this->session->data['user_id'] : 0;
	}

	/**
	 * Log Exception to a log file, if enabled
	 *
	 * @param $exception
	 */
	public function logEx($exception)
	{
		if ($this->config->get('emerchantpay_checkout_debug')) {
			$log = new Log('emerchantpay_checkout.log');
			$log->write($this->jTraceEx($exception));
		}
	}

	/**
	 * jTraceEx() - provide a Java style exception trace
	 * @param $exception Exception
	 * @param $seen - array passed to recursive calls to accumulate trace lines already seen
	 *                     leave as NULL when calling this function
	 * @return array of strings, one entry per trace line
	 *
	 * @SuppressWarnings(PHPMD)
	 */
	private function jTraceEx($exception, $seen = null)
	{
		$starter = ($seen) ? 'Caused by: ' : '';
		$result  = array();

		if (!$seen) $seen = array();

		$trace = $exception->getTrace();
		$prev  = $exception->getPrevious();

		$result[] = sprintf('%s%s: %s', $starter, get_class($exception), $exception->getMessage());

		$file = $exception->getFile();
		$line = $exception->getLine();

		while (true) {
			$current = "$file:$line";
			if (is_array($seen) && in_array($current, $seen)) {
				$result[] = sprintf(' ... %d more', count($trace) + 1);
				break;
			}
			$result[] = sprintf(' at %s%s%s(%s%s%s)',
				count($trace) && array_key_exists('class', $trace[0]) ? str_replace('\\', '.', $trace[0]['class']) : '',
				count($trace) && array_key_exists('class', $trace[0]) && array_key_exists('function', $trace[0]) ? '.' : '',
				count($trace) && array_key_exists('function', $trace[0]) ? str_replace('\\', '.', $trace[0]['function']) : '(main)',
				($line === null) ? $file : basename($file),
				($line === null) ? '' : ':',
				($line === null) ? '' : $line);
			if (is_array($seen))
				$seen[] = "$file:$line";
			if (!count($trace))
				break;
			$file = array_key_exists('file', $trace[0]) ? $trace[0]['file'] : 'Unknown Source';
			$line = (array_key_exists('file', $trace[0]) && array_key_exists('line', $trace[0]) && $trace[0]['line']) ? $trace[0]['line'] : null;
			array_shift($trace);
		}

		$result = join("\n", $result);

		if ($prev)
			$result .= "\n" . $this->jTraceEx($prev, $seen);

		return $result;
	}

	/**
	 * @param $transaction_type
	 * @return string
	 */
	private function getCustomParameterKey($transaction_type)
	{
		switch ($transaction_type) {
			case Types::PAY_PAL:
				$result = 'payment_type';
				break;
			case Types::GOOGLE_PAY:
			case Types::APPLE_PAY:
				$result = 'payment_subtype';
				break;
			default:
				$result = 'unknown';
		}

		return $result;
	}

	/**
	 * Order transaction types with Card Transaction types in front
	 *
	 * @param array $selected_types Selected transaction types
	 * @return array
	 */
	private function orderCardTransactionTypes($selected_types)
	{
		$custom_order = Types::getCardTransactionTypes();

		asort($selected_types);

		$sorted_array = array_intersect($custom_order, $selected_types);

		return array_merge($sorted_array, array_diff($selected_types, $sorted_array));
	}
}
