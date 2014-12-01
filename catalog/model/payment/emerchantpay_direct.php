<?php
class ModelPaymentEmerchantPayDirect extends Model
{
	const REQUEST_AUTHORIZE         = 1;
	const REQUEST_SALE              = 2;
	const REQUEST_INIT_RECURRING    = 3;

	const REQUEST_AUTHORIZE_3D      = 11;
	const REQUEST_SALE_3D           = 12;
	const REQUEST_INIT_RECURRING_3D = 13;


	/**
	 * Main method
	 *
	 * @param $address Order Address
	 * @param $total   Order Total
	 *
	 * @return array
	 */
	public function getMethod($address, $total) {
		$this->load->language('payment/emerchantpay_direct');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('emerchantpay_direct_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if ($this->config->get('emerchantpay_direct_total') > 0 && $this->config->get('emerchantpay_direct_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('emerchantpay_direct_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		$method_data = array();

		if ($status) {
			$method_data = array(
				'code'       => 'emerchantpay_direct',
				'title'      => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('emerchantpay_direct_sort_order')
			);
		}

		return $method_data;
	}

	/**
	 * Process Payment Data
	 *
	 * @param $order_info
	 * @param $sagepay_order_info
	 * @param $price
	 * @param $order_recurring_id
	 * @param $recurring_name
	 * @param null $i
	 *
	 * @return mixed
	 */
	private function processRecurringTransactions($order_info, $sagepay_order_info, $price, $order_recurring_id, $recurring_name, $i = null) {

		try {
			$genesis = new \Genesis\Genesis('Financial/Sale');

			$genesis
				->request()
					->setTransactionId(sprintf('%s-%s', $order_info['order_id'], md5(microtime(true))))
					->setRemoteIp($_SERVER['REMOTE_ADDR'])

					// Financial
					->setCurrency($this->currency->getCode())
					->setAmount($price)

					// Personal
					->setCustomerEmail($order_info['email'])
					->setCustomerPhone($order_info['telephone'])

					// CC
					->setCardHolder()
					->setCardNumber()
					->setCvv()
					->setExpirationMonth()
					->setExpirationYear()

					// Billing
					->setBillingFirstName($order_info['payment_firstname'])
					->setBillingLastName($order_info['payment_lastname'])
					->setBillingAddress1($order_info['payment_address_1'])
					->setBillingAddress2($order_info['payment_address_2'])
					->setBillingZipCode($order_info['payment_postcode'])
					->setBillingCity($order_info['payment_city'])
					->setBillingState($order_info['payment_zone_code'])
					->setBillingCountry($order_info['payment_iso_code_2'])

					// Shipping
					->setShippingFirstName($order_info['shipping_firstname'])
					->setShippingLastName($order_info['shipping_lastname'])
					->setShippingAddress1($order_info['shipping_address_1'])
					->setShippingAddress2($order_info['shipping_address_2'])
					->setShippingZipCode($order_info['shipping_postcode'])
					->setShippingCity($order_info['shipping_city'])
					->setShippingState($order_info['shipping_zone_code'])
					->setShippingCountry($order_info['shipping_iso_code_2']);

			$genesis->execute();

			$response = $genesis->response()->getResponseObject();

			var_dump($response->status);
			exit(0);
		}
		catch (Exception $e) {
			die($e->getMessage());
		}
	}

	/**
	 * Send transaction to Genesis
	 *
	 * @param $data array Transaction Data
	 *
	 * @return mixed
	 */
	public function sendTransaction($data) {
		if (!class_exists('\Genesis\Genesis')) {
			$this->bootstrapGenesis();
		}

		try {
			$genesis = null;

			switch ($this->config->get('emerchantpay_direct_transaction_type')) {
				case self::REQUEST_AUTHORIZE:
					$genesis = new \Genesis\Genesis( 'Financial\Authorize' );
					break;
				case self::REQUEST_AUTHORIZE_3D:
					$genesis = new \Genesis\Genesis( 'Financial\Authorize3D' );
					break;
				case self::REQUEST_INIT_RECURRING:
					$genesis = new \Genesis\Genesis( 'Financial\Recurring\InitRecurringSale' );
					break;
				case self::REQUEST_INIT_RECURRING_3D:
					$genesis = new \Genesis\Genesis( 'Financial\Recurring\InitRecurringSale3D' );
					break;
				case self::REQUEST_SALE:
					$genesis = new \Genesis\Genesis( 'Financial\Sale' );
					break;
				case self::REQUEST_SALE_3D:
					$genesis = new \Genesis\Genesis( 'Financial\Sale3D' );
					break;
			}

			$genesis
				->request()
					->setTransactionId($data['transaction_id'])
					->setRemoteIp($data['remote_address'])

					// Financial
					->setCurrency($data['currency'])
					->setAmount($data['amount'])

					// Personal
					->setCustomerEmail($data['customer_email'])
					->setCustomerPhone($data['customer_phone'])

					// CC
					->setCardHolder($data['card_holder'])
					->setCardNumber($data['card_number'])
					->setCvv($data['cvv'])
					->setExpirationMonth($data['expiration_month'])
					->setExpirationYear($data['expiration_year'])

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
					->setShippingCountry($data['shipping']['country']);

			if (in_array(
					$this->config->get('emerchantpay_direct_transaction_type'),
					array(
						self::REQUEST_AUTHORIZE_3D,
						self::REQUEST_SALE_3D,
						self::REQUEST_INIT_RECURRING_3D
					)
				)
			) {
				$genesis
					->request()
						->setNotificationUrl($this->url->link('payment/emerchantpay_direct/callback', '', 'SSL'))
						->setReturnSuccessUrl($this->url->link('payment/emerchantpay_direct/success', '', 'SSL'))
						->setReturnFailureUrl($this->url->link('payment/emerchantpay_direct/failure', '', 'SSL'));

			}

			$genesis->execute();

			if ($genesis->response()->isSuccessful()) {
				return $genesis->response()->getResponseObject();
			}
			else {
				return false;
			}
		}
		catch (Exception $e) {
			die($e->getMessage());
		}
	}

	/**
	 * Genesis Request - Reconcile
	 *
	 * @param $unique_id string - Id of a Genesis Transaction
	 *
	 * @return mixed
	 */
	public function reconcile($unique_id) {
		if (!class_exists('\Genesis\Genesis')) {
			$this->bootstrapGenesis();
		}

		try {
			$genesis = new \Genesis\Genesis('Reconcile\Transaction');

			$genesis
				->request()
					->setUniqueId($unique_id);

			$genesis->execute();

			if ($genesis->response()->isSuccessful()) {
				return $genesis->response()->getResponseObject();
			}

			return false;
		}
		catch (Exception $e) {
			//die($e->getMessage());
		}
	}

	public function convertCurrency($amount, $currency) {
		if (!class_exists('\Genesis\Genesis')) {
			$this->model_payment_emerchantpay_direct->bootstrapGenesis();
		}

		return \Genesis\Utils\Currency::exponentToReal($amount, $currency);
	}

	public function addTransaction($data) {
		$this->db->query("
			INSERT INTO
				`" . DB_PREFIX . "emerchantpay_direct_transactions`
			SET
				`unique_id` = '" . $data['unique_id'] . "',
				`order_id`  = '" . $data['order_id'] . "',
				`type` = '" . $data['type'] . "',
				`mode` = '" . $data['mode'] . "',
				`timestamp` = '" . $data['timestamp'] . "',
				`status` = '" . $data['status'] . "',
				`message` = '" . $data['message'] . "',
				`technical_message` = '" . $data['technical_message'] . "',
				`amount` = '" . $data['amount'] . "',
				`currency` = '" . $data['currency'] . "';
		");
	}

	public function updateTransaction($data) {
		$this->db->query("
			UPDATE
				`" . DB_PREFIX . "emerchantpay_direct_transactions`
			SET
				`type` = '" . $data['type'] . "',
				`mode` = '" . $data['mode'] . "',
				`timestamp` = '" . $data['timestamp'] . "',
				`status` = '" . $data['status'] . "',
				`message` = '" . $data['message'] . "',
				`technical_message` = '" . $data['technical_message'] . "',
				`amount` = '" . $data['amount'] . "',
				`currency` = '" . $data['currency'] . "'
			WHERE
				`unique_id` = '" . $data['unique_id'] . "';
		");
	}

	public function getTransactionById($reference_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "emerchantpay_direct_transactions` WHERE `unique_id` = '" . $this->db->escape($reference_id) . "' LIMIT 1");

		if ($query->num_rows) {
			return reset($query->rows);
		}

		return false;
	}

	/**
	 * Bootstrap Genesis Library
	 *
	 * @return void
	 */
	public function bootstrapGenesis() {
		include DIR_APPLICATION . '/model/payment/libraries/genesis_php/vendor/autoload.php';

		$environment = (intval($this->config->get('emerchantpay_direct_sandbox')) == 1 ? 'sandbox' : 'production');

		\Genesis\GenesisConfig::setUsername($this->config->get('emerchantpay_direct_username'));
		\Genesis\GenesisConfig::setPassword($this->config->get('emerchantpay_direct_password'));
		\Genesis\GenesisConfig::setToken($this->config->get('emerchantpay_direct_token'));
		\Genesis\GenesisConfig::setEnvironment($environment);
	}

	/**
	 * Declare support for Recurring Payments
	 *
	 * @return bool
	 */
	public function recurringPayments() {
		return true;
	}
}