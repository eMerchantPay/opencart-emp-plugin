<?php
class ModelPaymentEmerchantPayCheckout extends Model {
	public function install() {
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "emerchantpay_checkout_transactions` (
			  `unique_id` VARCHAR(255) NOT NULL,
			  `reference_id` VARCHAR(255) NOT NULL,
			  `order_id` INT(11) NOT NULL,
			  `type` CHAR(32) NOT NULL,
			  `mode` CHAR(255) NOT NULL,
			  `timestamp` DATETIME NOT NULL,
			  `status` CHAR(32) NOT NULL,
			  `message` VARCHAR(255) NULL,
			  `technical_message` VARCHAR(255) NULL,
			  `amount` DECIMAL( 10, 2 ) DEFAULT NULL,
			  `currency` CHAR(3) NULL,
			  PRIMARY KEY (`unique_id`)
			) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");
	}

	public function uninstall() {
		// Do nothing for now, destroying table with transactions is not a good idea
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "emerchantpay_checkout_transactions`;");
	}

	public function getTransactionById($reference_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "emerchantpay_checkout_transactions` WHERE `unique_id` = '" . $this->db->escape($reference_id) . "' LIMIT 1");

		if ($query->num_rows) {
			return reset($query->rows);
		}

		return false;
	}

	public function getTransactionsByOrder($order_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "emerchantpay_checkout_transactions` WHERE `order_id` = '" . intval($order_id) . "'");

		if ($query->num_rows) {
			return $query->rows;
		}

		return false;
	}

	public function addTransaction($data) {
		try {
			$this->db->query("
			INSERT INTO
				`" . DB_PREFIX . "emerchantpay_checkout_transactions`
			SET
				`unique_id` = '" . $data['unique_id'] . "',
				`reference_id` = '" . $data['reference_id'] . "',
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
		catch (Exception $exception) {
			$this->logEx($exception);
		}
	}

	public function capture($reference_id, $amount, $currency, $usage = '') {
		try {
			$this->bootstrap();

			$transaction_id = strtoupper(md5(microtime(true) . ':' . mt_rand()));
			$remote_ip      = $this->request->server['REMOTE_ADDR'];

			$genesis = new \Genesis\Genesis('Financial\Capture');

			$genesis
				->request()
					->setTransactionId($transaction_id)
					->setUsage($usage)
					->setRemoteIp($remote_ip)
					->setReferenceId($reference_id)
					->setAmount($amount)
					->setCurrency($currency);

			$genesis->execute();

			if ($genesis->response()->isSuccessful()) {
				$response = array(
					'error'     => false,
					'response'  => $genesis->response()->getResponseObject(),
					'message'   => strval($genesis->response()->getResponseObject()->message)
				);
			}
			else {
				$response = array(
					'error'     => true,
					'message'   => $genesis->response()->getErrorDescription()
				);
			}

			return (object)$response;
		}
		catch (Exception $exception) {
			$this->logEx($exception);
		}
	}

	public function refund($reference_id, $amount, $currency, $usage = '') {
		try {
			$this->bootstrap();

			$transaction_id = strtoupper(md5(microtime(true) . ':' . mt_rand()));
			$remote_ip      = $this->request->server['REMOTE_ADDR'];

			$genesis = new \Genesis\Genesis('Financial\Refund');

			$genesis
				->request()
					->setTransactionId($transaction_id)
					->setUsage($usage)
					->setRemoteIp($remote_ip)
					->setReferenceId($reference_id)
					->setAmount($amount)
					->setCurrency($currency);

			$genesis->execute();

			if ($genesis->response()->isSuccessful()) {
				$response = array(
					'error'     => false,
					'response'  => $genesis->response()->getResponseObject(),
					'message'   => strval($genesis->response()->getResponseObject()->message)
				);
			}
			else {
				$response = array(
					'error'     => true,
					'message'   => $genesis->response()->getErrorDescription()
				);
			}

			return (object)$response;
		}
		catch (Exception $exception) {
			$this->logEx($exception);
		}
	}

	public function void($reference_id, $usage = '') {
		try {
			$this->bootstrap();

			$transaction_id = strtoupper(md5(microtime(true) . ':' . mt_rand()));
			$remote_ip      = $this->request->server['REMOTE_ADDR'];

			$genesis = new \Genesis\Genesis('Financial\Void');

			$genesis
				->request()
					->setTransactionId($transaction_id)
					->setUsage($usage)
					->setRemoteIp($remote_ip)
					->setReferenceId($reference_id);

			$genesis->execute();

			if ($genesis->response()->isSuccessful()) {
				$response = array(
					'error'     => false,
					'response'  => $genesis->response()->getResponseObject(),
					'message'   => strval($genesis->response()->getResponseObject()->message)
				);
			}
			else {
				$response = array(
					'error'     => true,
					'message'   => $genesis->response()->getErrorDescription()
				);
			}

			return (object)$response;
		}
		catch (Exception $exception) {
			$this->logEx($exception);
		}
	}

	public function getTransactionTypes() {
		$this->load->language('payment/emerchantpay_checkout');

		return array(
			'authorize'    => array (
				'id'    => 1,
				'name'  => $this->language->get('text_transaction_authorize')
			),
			'authorize_3d'    => array (
				'id'    => 11,
				'name'  => $this->language->get('text_transaction_authorize_3d')
			),
			'sale'    => array (
				'id'    => 2,
				'name'  => $this->language->get('text_transaction_sale')
			),
			'sale_3d'    => array (
				'id'    => 12,
				'name'  => $this->language->get('text_transaction_sale_3d')
			),
			/*
			'init_recurring'    => array (
				'id'    => 3,
				'name'  => $this->language->get('text_transaction_init_recurring')
			),
			'init_recurring_3d'    => array (
				'id'    => 13,
				'name'  => $this->language->get('text_transaction_init_recurring_3d')
			),
			*/
		);
	}

	/**
	 * Convert ISO-4217 to float
	 *
	 * @param $amount
	 * @param $currency
	 *
	 * @return mixed
	 */
	public function iso4217ConvertAmount($amount, $currency) {
		$this->bootstrap();

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
			include DIR_CATALOG . '/model/payment/libraries/genesis_php/vendor/autoload.php';

			$environment = intval($this->config->get('emerchantpay_direct_sandbox')) == 1 ? 'sandbox' : 'production';

			\Genesis\GenesisConfig::setUsername($this->config->get('emerchantpay_direct_username'));
			\Genesis\GenesisConfig::setPassword($this->config->get('emerchantpay_direct_password'));
			\Genesis\GenesisConfig::setToken($this->config->get('emerchantpay_direct_token'));
			\Genesis\GenesisConfig::setEnvironment($environment);
		}
	}

	/**
	 * Log Exception to a log file, if enabled
	 *
	 * @param $exception
	 */
	public function logEx($exception) {
		if ($this->config->get('emerchantpay_checkout_debug')) {
			$log = new Log('emerchantpay_checkout.log');
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