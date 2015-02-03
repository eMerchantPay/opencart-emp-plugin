<?php
class ControllerPaymentEmerchantPayDirect extends Controller {
	public function index() {
		$this->load->language('payment/emerchantpay_direct');

		$data['text_credit_card'] = $this->language->get('text_credit_card');
		$data['text_loading'] = $this->language->get('text_loading');
		$data['text_card_type'] = $this->language->get('text_card_type');
		$data['text_card_name'] = $this->language->get('text_card_name');
		$data['text_card_digits'] = $this->language->get('text_card_digits');
		$data['text_card_expiry'] = $this->language->get('text_card_expiry');

		$data['entry_card'] = $this->language->get('entry_card');
		$data['entry_card_existing'] = $this->language->get('entry_card_existing');
		$data['entry_card_new'] = $this->language->get('entry_card_new');
		$data['entry_card_save'] = $this->language->get('entry_card_save');
		$data['entry_cc_owner'] = $this->language->get('entry_cc_owner');
		$data['entry_cc_type'] = $this->language->get('entry_cc_type');
		$data['entry_cc_number'] = $this->language->get('entry_cc_number');
		$data['entry_cc_start_date'] = $this->language->get('entry_cc_start_date');
		$data['entry_cc_expire_date'] = $this->language->get('entry_cc_expire_date');
		$data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');
		$data['entry_cc_issue'] = $this->language->get('entry_cc_issue');
		$data['entry_cc_choice'] = $this->language->get('entry_cc_choice');

		$data['error_payments'] = (isset($this->error['warning'])) ? $this->error['warning'] : '';

		$data['help_start_date'] = $this->language->get('help_start_date');
		$data['help_issue'] = $this->language->get('help_issue');

		$data['button_confirm'] = $this->language->get('button_confirm');

		$data['cards'] = array();

		$data['months'] = array();

		for ($i = 1; $i <= 12; $i++) {
			$data['months'][] = array(
				'text' => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)),
				'value' => sprintf('%02d', $i)
			);
		}

		$today = getdate();

		$data['year_valid'] = array();

		for ($i = $today['year'] - 10; $i < $today['year'] + 1; $i++) {
			$data['year_valid'][] = array(
				'text' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
				'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i))
			);
		}

		$data['year_expire'] = array();

		for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
			$data['year_expire'][] = array(
				'text' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
				'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i))
			);
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/emerchantpay_direct.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/payment/emerchantpay_direct.tpl', $data);
		} else {
			return $this->load->view('default/template/payment/emerchantpay_direct.tpl', $data);
		}
	}

	/**
	 * Process order confirmation
	 *
	 * @return void
	 */
	public function send() {
		$this->load->model('checkout/order');
		$this->load->model('payment/emerchantpay_direct');

		$this->load->language('payment/emerchantpay_direct');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		try {

			$data = array(
				'transaction_id'    => $order_info['order_id'] . '-' . strtoupper(md5(microtime(true) . ':' . mt_rand())),
				'remote_address'    => $this->request->server['REMOTE_ADDR'],

				'currency'          => $this->currency->getCode(),
				'amount'            => $order_info['total'],

				'customer_email'    => $order_info['email'],
				'customer_phone'    => $order_info['telephone'],

				'card_holder'       => $this->request->post['emerchantpay_direct-cc-holder'],
				'card_number'       => $this->ccFilter($this->request->post['emerchantpay_direct-cc-number'], 'number'),
				'cvv'               => $this->ccFilter($this->request->post['emerchantpay_direct-cc-cvv'], 'cvv'),
				'expiration_month'  => $this->ccFilter($this->request->post['emerchantpay_direct-cc-expiration'], 'month'),
				'expiration_year'   => $this->ccFilter($this->request->post['emerchantpay_direct-cc-expiration'], 'year'),

				'billing'           => array(
					'first_name'    => $order_info['payment_firstname'],
					'last_name'     => $order_info['payment_lastname'],
					'address1'      => $order_info['payment_address_1'],
					'address2'      => $order_info['payment_address_2'],
					'zip'           => $order_info['payment_postcode'],
					'city'          => $order_info['payment_city'],
					'state'         => $order_info['payment_zone_code'],
					'country'       => $order_info['payment_iso_code_2'],
				),

				'shipping'           => array(
					'first_name'    => $order_info['shipping_firstname'],
					'last_name'     => $order_info['shipping_lastname'],
					'address1'      => $order_info['shipping_address_1'],
					'address2'      => $order_info['shipping_address_2'],
					'zip'           => $order_info['shipping_postcode'],
					'city'          => $order_info['shipping_city'],
					'state'         => $order_info['shipping_zone_code'],
					'country'       => $order_info['shipping_iso_code_2'],
				)
			);

			$transaction = $this->model_payment_emerchantpay_direct->sendTransaction($data);

			if (isset($transaction->response)) {
				$amount = $this->model_payment_emerchantpay_direct->convertCurrency(
					$transaction->response->amount,
					$transaction->response->currency
				);

				$data = array(
					'order_id'          => $order_info['order_id'],
					'unique_id'         => $transaction->response->unique_id,
					'reference_id'      => '0',
					'type'              => $transaction->response->transaction_type,
					'mode'              => $transaction->response->mode,
					'timestamp'         => $transaction->response->timestamp,
					'status'            => $transaction->response->status,
					'message'           => $transaction->response->message,
					'technical_message' => $transaction->response->technical_message,
					'amount'            => $amount,
					'currency'          => $transaction->response->currency,
				);

				$this->model_payment_emerchantpay_direct->addTransaction($data);

				if (!$transaction->error) {

					if (isset($transaction->response->redirect_url)) {
						$this->model_checkout_order->addOrderHistory(
							$this->session->data['order_id'],
							$this->config->get('emerchantpay_direct_async_order_status_id'),
							$this->language->get('text_payment_status_init_async'),
							false
						);

						$redirect_url = strval($transaction->response->redirect_url);
					}
					else {

						$this->model_checkout_order->addOrderHistory(
							$this->session->data['order_id'],
							$this->config->get('emerchantpay_direct_order_status_id'),
							$this->language->get('text_payment_status_successful'),
							false
						);

						$redirect_url = $this->url->link('checkout/success', '', 'SSL');
					}

					$json = array(
						'redirect' => $redirect_url
					);
				}
				else {
					/*
					$this->model_checkout_order->addOrderHistory(
						$this->session->data['order_id'],
						$this->config->get('emerchantpay_direct_order_failure_status_id'),
						$this->language->get('text_payment_status_init_failed'),
						false
					);
					*/

					$json = array(
						'error' => $this->language->get('text_payment_failure')
					);
				}
			}
			else {
				$json = array(
					'error' => $this->language->get('text_payment_system_error')
				);
			}
		}
		catch (Exception $exception) {
			$json = array(
				'error' => $this->language->get('text_payment_system_error')
			);

			$this->model_payment_emerchantpay_checkout->logEx($exception);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Process Async-transaction Notification
	 *
	 * @return void
	 */
	public function callback() {
		$this->load->model('checkout/order');
		$this->load->model('payment/emerchantpay_direct');

		$this->load->language('payment/emerchantpay_direct');

		try {
			$this->model_payment_emerchantpay_direct->bootstrap();

			$notification = new \Genesis\API\Notification();

			$notification->parseNotification( $this->request->post );

			if ( $notification->isAuthentic() || !$notification->isAuthentic() ) {
				$reconcile = $this->model_payment_emerchantpay_direct->reconcile(
					$notification->getParsedNotification()->unique_id
				);

				if (isset($reconcile->response)) {

					$transaction = $this->model_payment_emerchantpay_direct->getTransactionById($reconcile->response->unique_id);

					if ( isset( $transaction['order_id'] ) && intval($transaction['order_id']) > 0 ) {

						// Check if somehow we already have this transaction in our database
						$transactionEntry = $this->model_payment_emerchantpay_direct->getTransactionById(
							$reconcile->response->unique_id
						);

						if (isset($transactionEntry['order_id'])) {
							$amount = $this->model_payment_emerchantpay_direct->convertCurrency(
								$reconcile->response->amount,
								$reconcile->response->currency
							);

							$data = array(
								'order_id'          => $transaction['order_id'],
								'unique_id'         => $reconcile->response->unique_id,
								'reference_id'      => '0',
								'type'              => $reconcile->response->transaction_type,
								'mode'              => $reconcile->response->mode,
								'timestamp'         => $reconcile->response->timestamp,
								'status'            => $reconcile->response->status,
								'currency'          => $reconcile->response->currency,
								'amount'            => $amount,
								'message'           => isset($reconcile->response->message) ? $reconcile->response->message : '',
								'technical_message' => isset($reconcile->response->technical_message) ? $reconcile->response->technical_message : '',
							);

							$this->model_payment_emerchantpay_direct->editTransaction($data);

							if ($reconcile->response->status == 'approved') {
								$this->model_checkout_order->addOrderHistory(
									$transaction['order_id'],
									$this->config->get('emerchantpay_direct_order_status_id'),
									$this->language->get('text_payment_status_successful')
								);
							}
							else {
								$this->model_checkout_order->addOrderHistory(
									$transaction['order_id'],
									$this->config->get('emerchantpay_direct_failure_order_status_id'),
									$this->language->get('text_payment_status_unsuccessful')
								);
							}
						}

						$this->response->addHeader('Content-Type: text/xml');
						$this->response->setOutput($notification->getEchoResponse());
					}
				}
			}
		}
		catch(Exception $exception) {
			$this->model_payment_emerchantpay_direct->logEx($exception);
		}
	}

	/**
	 * 3D Callback
	 *
	 * @return void
	 */
	/*
	public function callback() {
		$this->load->model('checkout/order');
		$this->load->model('payment/emerchantpay_direct');

		$this->load->language('payment/emerchantpay_direct');

		try {
			if (!class_exists('\Genesis\Genesis')) {
				$this->model_payment_emerchantpay_direct->bootstrapGenesis();
			}

			$notification = new \Genesis\API\Notification();

			$notification->parseNotification( $this->request->post );

			if ( $notification->isAuthentic() ) {
				$reconcile = $this->model_payment_emerchantpay_direct->reconcile($notification->getParsedNotification()->unique_id);

				$transaction = $this->model_payment_emerchantpay_direct->getTransactionById($reconcile->unique_id);

				if ( isset( $transaction['order_id'] ) ) {

					if (strval($reconcile->status) == 'approved') {
						$status_id = $this->config->get( 'emerchantpay_direct_order_status_id' );
						$text      = $this->language->get('text_payment_successful');
					} else {
						$status_id = $this->config->get( 'emerchantpay_direct_order_status_id' );
						$text      = $this->language->get('text_payment_unsuccessful');
					}

					$this->model_checkout_order->addOrderHistory($transaction['order_id'], $status_id, $text);

					$data = array(
						'unique_id'         => $reconcile->unique_id,
						'type'              => $reconcile->transaction_type,
						'mode'              => $reconcile->mode,
						'timestamp'         => $reconcile->timestamp,
						'status'            => $reconcile->status,
						'currency'          => $reconcile->currency,
						'amount'            => $this->model_payment_emerchantpay_direct->convertCurrency($reconcile->amount, $reconcile->currency),
						'message'           => isset($reconcile->message) ? $reconcile->message : '',
						'technical_message' => isset($reconcile->technical_message) ? $reconcile->technical_message : '',
					);

					$this->model_payment_emerchantpay_direct->updateTransaction($data);

					$this->response->addHeader('Content-Type: text/xml');
					$this->response->setOutput($notification->getEchoResponse());
				}
			}
		}
		catch(Exception $e) {
			die($e->getMessage());
			$this->model_payment_emerchantpay_direct->logEx($exception);
		}
	}
	*/

	/**
	 * Sanitize incoming CC data
	 *
	 * @param $input
	 * @param $type
	 *
	 * @return mixed|string
	 */
	public function ccFilter($input, $type) {
		switch($type) {
			case 'name':
				return $input;
				break;
			case 'number':
				return str_replace(' ', '', $input);
				break;
			case 'cvv':
				return substr(strval($input), 0, 3);
			case 'year':
				@list($month, $year) = explode('/', $input);

				$month  = trim($month);
				$year   = trim($year);

				if (isset($year) && strlen($year) > 0) {
					if (strlen($year) == 2) {
						return sprintf('20%s', strval($year));
					}
					else {
						return substr(strval($year), 0, 4);
					}
				}
				break;
			case 'month':
				@list($month, $year) = explode('/', $input);

				if (isset($month) && strlen($month) > 0) {
					return substr(strval($month), 0, 2);
				}
				break;
			default:
				return $input;
				break;
		}
	}

	/**
	 * Async Transaction Redirect for Successful Payment
	 *
	 * @return void
	 */
	public function success() {
		if (isset($this->session->data['order_id'])) {
			$this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
		}
		else {
			$this->response->redirect($this->url->link('account/login', '', 'SSL'));
		}
	}

	/**
	 * Async Transaction Redirect for Failed Payment
	 *
	 * @return void
	 */
	public function failure() {
		$this->load->language('payment/emerchantpay_checkout');

		$this->session->data['error'] = $this->language->get('text_payment_failure');

		$this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
	}
}