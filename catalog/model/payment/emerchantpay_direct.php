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
	 * Add transaction to the database
	 *
	 * @param $data array
	 */
	public function addTransaction($data) {
		try {
			foreach($data as $column => &$value) {
				$value = $this->db->escape($value);
			}

			$this->db->query("
				INSERT INTO
					`" . DB_PREFIX . "emerchantpay_direct_transactions`
				SET
					`unique_id` = '" . $data['unique_id'] . "',
					`order_id`  = '" . $data['order_id'] . "',
					`reference_id` = '" . $data['reference_id'] . "',
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
		catch (Exception $exception) {
			$this->logEx($exception);
		}
	}


	/**
	 * Update transaction inside the database
	 *
	 * @param $data array
	 */
	public function editTransaction($data) {
		try {
			foreach($data as $column => &$value) {
				$value = $this->db->escape($value);
			}

			$this->db->query("
				UPDATE
					`" . DB_PREFIX . "emerchantpay_direct_transactions`
				SET
					`reference_id` = '" . $data['reference_id'] . "',
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
		catch (Exception $exception) {
			$this->logEx($exception);
		}
	}

	/**
	 * Get saved transaction (from DB) by id
	 *
	 * @param $reference_id
	 *
	 * @return bool|mixed
	 */
	public function getTransactionById($reference_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "emerchantpay_direct_transactions` WHERE `unique_id` = '" . $this->db->escape($reference_id) . "' LIMIT 1");

		if ($query->num_rows) {
			return reset($query->rows);
		}

		return false;
	}

	/**
	 * Send transaction to Genesis
	 *
	 * @param $data array Transaction Data
	 *
	 * @return mixed
	 */
	public function sendTransaction($data) {
		try {
			$this->bootstrap();

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
				default:
					$genesis = null;
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
						self::REQUEST_AUTHORIZE_3D, self::REQUEST_SALE_3D, self::REQUEST_INIT_RECURRING_3D
					)
				))
			{
				$genesis
					->request()
						->setNotificationUrl($this->url->link('payment/emerchantpay_direct/callback', '', 'SSL'))
						->setReturnSuccessUrl($this->url->link('payment/emerchantpay_direct/success', '', 'SSL'))
						->setReturnFailureUrl($this->url->link('payment/emerchantpay_direct/failure', '', 'SSL'));

			}

			$genesis->execute();

			if ($genesis->response()->isSuccessful()) {
				$response = array(
					'error'     => false,
					'message'   => strval($genesis->response()->getResponseObject()->message),
					'response'  => $genesis->response()->getResponseObject()
				);
			}
			else {
				$response = array(
					'error'     => true,
					'message'   => strval($genesis->response()->getResponseObject()->message),
					'response'  => $genesis->response()->getResponseObject()
				);
			}

			return (object)$response;
		}
		catch (Exception $exception) {
			$this->logEx($exception);
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
		try {
			$this->bootstrap();

			$genesis = new \Genesis\Genesis('Reconcile\Transaction');

			$genesis
				->request()
					->setUniqueId($unique_id);

			$genesis->execute();

			$response = array(
				'message'   => strval($genesis->response()->getResponseObject()->message),
				'response'  => $genesis->response()->getResponseObject()
			);

			return (object)$response;
		}
		catch (Exception $exception) {
			$this->logEx($exception);
		}
	}

	public function convertCurrency($amount, $currency) {
		if (!class_exists('\Genesis\Genesis')) {
			$this->model_payment_emerchantpay_direct->bootstrapGenesis();
		}

		return \Genesis\Utils\Currency::exponentToReal($amount, $currency);
	}

	/**
	 * Bootstrap Genesis Library
	 *
	 * @return void
	 */
	public function bootstrap() {
		// Look for, but DO NOT try to load via Autoloader magic methods
		if (!class_exists('\Genesis\Genesis', false)) {
			include DIR_APPLICATION . '/model/payment/libraries/genesis_php/vendor/autoload.php';

			$environment = ( intval( $this->config->get( 'emerchantpay_direct_sandbox' ) ) == 1 ? 'sandbox' : 'production' );

			\Genesis\GenesisConfig::setUsername( $this->config->get( 'emerchantpay_direct_username' ) );
			\Genesis\GenesisConfig::setPassword( $this->config->get( 'emerchantpay_direct_password' ) );
			\Genesis\GenesisConfig::setToken( $this->config->get( 'emerchantpay_direct_token' ) );

			\Genesis\GenesisConfig::setEnvironment( $environment );
		}
	}

	/**
	 * Log Exception to a log file, if enabled
	 *
	 * @param $exception
	 */
	public function logEx($exception) {
		if ($this->config->get('emerchantpay_direct_debug')) {
			$log = new Log('emerchantpay_direct.log');
			$log->write($this->jTraceEx($exception));
		}
	}

	/**
	 * jTraceEx() - provide a Java style exception trace
	 * @param $e Exception
	 * @param $seen      - array passed to recursive calls to accumulate trace lines already seen
	 *                     leave as NULL when calling this function
	 * @return array of strings, one entry per trace line
	 */
	private function jTraceEx($e, $seen=null) {
		$starter = $seen ? 'Caused by: ' : '';
		$result = array();

		if (!$seen) $seen = array();

		$trace  = $e->getTrace();
		$prev   = $e->getPrevious();

		$result[] = sprintf('%s%s: %s', $starter, get_class($e), $e->getMessage());

		$file = $e->getFile();
		$line = $e->getLine();

		while (true) {
			$current = "$file:$line";
			if (is_array($seen) && in_array($current, $seen)) {
				$result[] = sprintf(' ... %d more', count($trace)+1);
				break;
			}
			$result[] = sprintf(' at %s%s%s(%s%s%s)',
				count($trace) && array_key_exists('class', $trace[0]) ? str_replace('\\', '.', $trace[0]['class']) : '',
				count($trace) && array_key_exists('class', $trace[0]) && array_key_exists('function', $trace[0]) ? '.' : '',
				count($trace) && array_key_exists('function', $trace[0]) ? str_replace('\\', '.', $trace[0]['function']) : '(main)',
				$line === null ? $file : basename($file),
				$line === null ? '' : ':',
				$line === null ? '' : $line);
			if (is_array($seen))
				$seen[] = "$file:$line";
			if (!count($trace))
				break;
			$file = array_key_exists('file', $trace[0]) ? $trace[0]['file'] : 'Unknown Source';
			$line = array_key_exists('file', $trace[0]) && array_key_exists('line', $trace[0]) && $trace[0]['line'] ? $trace[0]['line'] : null;
			array_shift($trace);
		}

		$result = join("\n", $result);

		if ($prev)
			$result  .= "\n" . $this->jTraceEx($prev, $seen);

		return $result;
	}
}