<?php
class ModelPaymentEmerchantPayDirect extends Model {
	public function install() {
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "emerchantpay_direct_transactions` (
			  `unique_id` VARCHAR(255) NOT NULL,
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
		//$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "emerchantpay_direct_transactions`;");
	}

	public function getTransactionById($reference_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "emerchantpay_direct_transactions` WHERE `unique_id` = '" . $this->db->escape($reference_id) . "' LIMIT 1");

		if ($query->num_rows) {
			return reset($query->rows);
		}

		return false;
	}

	public function getTransactionsByOrder($order_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "emerchantpay_direct_transactions` WHERE `order_id` = '" . intval($order_id) . "' ORDER BY `timestamp` DESC");

		if ($query->num_rows) {
			return $query->rows;
		}

		return false;
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

	public function capture($reference_id, $amount, $currency, $usage = '') {
		if (!class_exists('\Genesis\Genesis')) {
			$this->bootstrapGenesis();
		}

		try {
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
				return $genesis->response()->getResponseObject();
			}

			return false;
		}
		catch (Exception $e) {
			return false;
		}
	}

	public function refund($reference_id, $amount, $currency, $usage = '') {
		if (!class_exists('\Genesis\Genesis')) {
			$this->bootstrapGenesis();
		}

		try {
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
				return $genesis->response()->getResponseObject();
			}

			return false;
		}
		catch (Exception $e) {
			return false;
		}
	}

	public function void($reference_id, $usage = '') {
		if (!class_exists('\Genesis\Genesis')) {
			$this->bootstrapGenesis();
		}

		try {
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
				return $genesis->response()->getResponseObject();
			}

			return false;
		}
		catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function getTransactionTypes() {
		$this->load->language('payment/emerchantpay_direct');

		return array(
			'authorize'    => array (
				'id'    => 1,
				'name'  => $this->language->get('text_transaction_authorize')
			),
			'sale'    => array (
				'id'    => 2,
				'name'  => $this->language->get('text_transaction_sale')
			),
			'init_recurring'    => array (
				'id'    => 3,
				'name'  => $this->language->get('text_transaction_init_recurring')
			),
			'authorize_3d'    => array (
				'id'    => 11,
				'name'  => $this->language->get('text_transaction_authorize_3d')
			),
			'sale_3d'    => array (
				'id'    => 12,
				'name'  => $this->language->get('text_transaction_sale_3d')
			),
			'init_recurring_3d'    => array (
				'id'    => 13,
				'name'  => $this->language->get('text_transaction_init_recurring_3d')
			),
		);
	}

	public function bootstrapGenesis() {
		include DIR_CATALOG . '/model/payment/libraries/genesis_php/vendor/autoload.php';

		$environment = intval($this->config->get('emerchantpay_direct_sandbox')) == 1 ? 'sandbox' : 'production';

		\Genesis\GenesisConfig::setUsername($this->config->get('emerchantpay_direct_username'));
		\Genesis\GenesisConfig::setPassword($this->config->get('emerchantpay_direct_password'));
		\Genesis\GenesisConfig::setToken($this->config->get('emerchantpay_direct_token'));
		\Genesis\GenesisConfig::setEnvironment($environment);
	}
}