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

		if ($this->config->get('sagepay_direct_card') == '1') {
			$data['sagepay_direct_card'] = true;
		} else {
			$data['sagepay_direct_card'] = false;
		}

		$data['existing_cards'] = array();
		if ($this->customer->isLogged() && $data['sagepay_direct_card']) {
			$this->load->model('payment/sagepay_direct');
			$data['existing_cards'] = $this->model_payment_sagepay_direct->getCards($this->customer->getId());
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/emerchantpay_direct.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/payment/emerchantpay_direct.tpl', $data);
		} else {
			return $this->load->view('default/template/payment/emerchantpay_direct.tpl', $data);
		}
	}

	public function send() {
		$this->load->model('checkout/order');
		$this->load->model('payment/emerchantpay_direct');

		$this->load->language('payment/emerchantpay_direct');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$expiration = explode('/', $this->request->post['emerchantpay_direct-cc-expiration']);

		$json = '';

		try {

			$data = array(
				'transaction_id'    => $order_info['order_id'] . '-' . strtoupper(md5(microtime(true) . ':' . mt_rand())),
				'remote_address'    => $this->request->server['REMOTE_ADDR'],

				'currency'          => $this->currency->getCode(),
				'amount'            => $order_info['total'],

				'customer_email'    => $order_info['email'],
				'customer_phone'    => $order_info['telephone'],

				'card_holder'       => $this->request->post['emerchantpay_direct-cc-holder'],
				'card_number'       => str_replace(' ', '', $this->request->post['emerchantpay_direct-cc-number']),
				'cvv'               => intval($this->request->post['emerchantpay_direct-cc-cvv']),
				'expiration_month'  => trim($expiration[0]),
				'expiration_year'   => '20' . trim($expiration[1]),

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

			$response = $this->model_payment_emerchantpay_direct->sendTransaction($data);

			if (is_object($response)) {
				$data = array(
					'order_id'          => $order_info['order_id'],
					'unique_id'         => $response->unique_id,
					'type'              => $response->transaction_type,
					'mode'              => $response->mode,
					'timestamp'         => $response->timestamp,
					'status'            => $response->status,
					'message'           => $response->message,
					'technical_message' => $response->technical_message,
					'amount'            => $this->model_payment_emerchantpay_direct->convertCurrency($response->amount, $response->currency),
					'currency'          => $response->currency,
				);

				$this->model_payment_emerchantpay_direct->addTransaction($data);

				$this->model_checkout_order->addOrderHistory(
					$this->session->data['order_id'],
					$this->config->get('emerchantpay_direct_order_status_id'),
					$this->language->get('text_payment_successful'),
					false
				);

				if (isset($response->redirect_url)) {
					$json['redirect'] = strval($response->redirect_url);
				}
				else {
					$json['redirect'] = $this->url->link('checkout/success', '', 'SSL');
				}
			}
			else {
				$this->model_checkout_order->addOrderHistory(
					$this->session->data['order_id'],
					$this->config->get('emerchantpay_direct_order_status_id'),
					$this->language->get('text_payment_unsuccessful'),
					false
				);

				$json['error'] = $this->language->get('text_failure_generic');
			}
		}
		catch (Exception $e) {
			$json['error'] = $this->language->get('text_system_error');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * 3D Callback
	 *
	 * @return void
	 */
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
		}
	}

	/**
	 * 3D success redirect
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
	 * 3D failure redirect
	 *
	 * @return void
	 */
	public function failure() {
		$this->load->language('payment/emerchantpay_direct');

		$this->session->data['error'] = $this->language->get('text_failure_generic');

		$this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
	}
}