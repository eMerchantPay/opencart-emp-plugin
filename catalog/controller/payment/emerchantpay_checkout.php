<?php
class ControllerPaymentEmerchantPayCheckout extends Controller {
	public function index() {
		$this->load->language('payment/emerchantpay_checkout');

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

		if ($this->config->get('sagepay_checkout_card') == '1') {
			$data['sagepay_checkout_card'] = true;
		} else {
			$data['sagepay_checkout_card'] = false;
		}

		$data['existing_cards'] = array();
		if ($this->customer->isLogged() && $data['sagepay_checkout_card']) {
			$this->load->model('payment/sagepay_checkout');
			$data['existing_cards'] = $this->model_payment_sagepay_checkout->getCards($this->customer->getId());
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/emerchantpay_checkout.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/payment/emerchantpay_checkout.tpl', $data);
		} else {
			return $this->load->view('default/template/payment/emerchantpay_checkout.tpl', $data);
		}
	}

	/**
	 * Process order confirmation
	 *
	 * @return void
	 */
	public function send() {
		$this->load->model('checkout/order');
		$this->load->model('payment/emerchantpay_checkout');

		$this->load->language('payment/emerchantpay_checkout');

		try {
			$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

			$data = array(
				'transaction_id'        => strtoupper(md5(microtime(true) . mt_rand())),
				'remote_address'        => $this->request->server['REMOTE_ADDR'],

				'currency'              => $this->currency->getCode(),
				'amount'                => $order_info['total'],

				'customer_email'        => $order_info['email'],
				'customer_phone'        => $order_info['telephone'],

				'notification_url'      => $this->url->link('payment/emerchantpay_checkout/callback', '', 'SSL'),
				'return_success_url'    => $this->url->link('payment/emerchantpay_checkout/success', '', 'SSL'),
				'return_failure_url'    => $this->url->link('payment/emerchantpay_checkout/failure', '', 'SSL'),
				'return_cancel_url'     => $this->url->link('payment/emerchantpay_checkout/cancel', '', 'SSL'),

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

			$transaction = $this->model_payment_emerchantpay_checkout->create($data);

			if (isset($transaction->response)) {
				$amount = $this->model_payment_emerchantpay_checkout->convertCurrency(
					$transaction->response->amount,
					$transaction->response->currency
				);

				$data = array(
					'order_id'          => $order_info['order_id'],
					'unique_id'         => $transaction->response->unique_id,
					'reference_id'      => '0',
					'type'              => 'wpf_create',
					'mode'              => $transaction->response->mode,
					'timestamp'         => $transaction->response->timestamp,
					'status'            => $transaction->response->status,
					'message'           => $transaction->response->message,
					'technical_message' => $transaction->response->technical_message,
					'amount'            => $amount,
					'currency'          => $transaction->response->currency,
				);

				$this->model_payment_emerchantpay_checkout->addTransaction($data);

				if ($transaction->error) {
					/*
					$this->model_checkout_order->addOrderHistory(
						$this->session->data['order_id'],
						$this->config->get('emerchantpay_checkout_order_failure_status_id'),
						$this->language->get('text_payment_status_init_failed'),
						false
					);
					*/

					$json = array(
						'error' => $this->language->get('text_payment_failure')
					);
				}
				else {
					$this->model_checkout_order->addOrderHistory(
						$this->session->data['order_id'],
						$this->config->get('emerchantpay_checkout_order_status_id'),
						$this->language->get('text_payment_status_initiated'),
						false
					);

					$json = array(
						'redirect' => strval($transaction->response->redirect_url)
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
		$this->load->model('payment/emerchantpay_checkout');

		$this->load->language('payment/emerchantpay_checkout');

		try {
			$this->model_payment_emerchantpay_checkout->bootstrap();

			$notification = new \Genesis\API\Notification();

			$notification->parseNotification( $this->request->post );

			if ( $notification->isAuthentic() || !$notification->isAuthentic() ) {
				$wpf_reconcile = $this->model_payment_emerchantpay_checkout->reconcile(
					$notification->getParsedNotification()->wpf_unique_id
				);

				if (isset($wpf_reconcile->response)) {

					$transaction = $this->model_payment_emerchantpay_checkout->getTransactionById($wpf_reconcile->response->unique_id);

					if ( isset( $transaction['order_id'] ) && intval($transaction['order_id']) > 0 ) {

						// Check if somehow we already have this transaction in our database
						$transactionEntry = $this->model_payment_emerchantpay_checkout->getTransactionById(
							$wpf_reconcile->response->payment_transaction->unique_id
						);

						if (!isset($transactionEntry['order_id'])) {
							$amount = $this->model_payment_emerchantpay_checkout->convertCurrency(
								$wpf_reconcile->response->payment_transaction->amount,
								$wpf_reconcile->response->payment_transaction->currency
							);

							$data = array(
								'order_id'          => $transaction['order_id'],
								'unique_id'         => $wpf_reconcile->response->payment_transaction->unique_id,
								'reference_id'      => $wpf_reconcile->response->unique_id,
								'type'              => $wpf_reconcile->response->payment_transaction->transaction_type,
								'mode'              => $wpf_reconcile->response->payment_transaction->mode,
								'timestamp'         => $wpf_reconcile->response->payment_transaction->timestamp,
								'status'            => $wpf_reconcile->response->payment_transaction->status,
								'currency'          => $wpf_reconcile->response->payment_transaction->currency,
								'amount'            => $amount,
								'message'           => isset($wpf_reconcile->response->payment_transaction->message) ? $wpf_reconcile->response->payment_transaction->message : '',
								'technical_message' => isset($wpf_reconcile->response->payment_transaction->technical_message) ? $wpf_reconcile->response->payment_transaction->technical_message : '',
							);

							$this->model_payment_emerchantpay_checkout->addTransaction($data);

							if ($wpf_reconcile->response->payment_transaction->status == 'approved') {
								$this->model_checkout_order->addOrderHistory(
									$transaction['order_id'],
									$this->config->get('emerchantpay_checkout_order_status_id'),
									$this->language->get('text_payment_status_successful')
								);
							}
							else {
								$this->model_checkout_order->addOrderHistory(
									$transaction['order_id'],
									$this->config->get('emerchantpay_checkout_order_failure_status_id'),
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
			$this->model_payment_emerchantpay_checkout->logEx($exception);
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

	/**
	 * Async Transaction Redirect for Cancelled Payment
	 *
	 * @TODO test
	 *
	 * @return void
	 */
	public function cancel() {
		if (isset($this->session->data['order_id'])) {
			$this->response->redirect($this->url->link('checkout/cancel', '', 'SSL'));
		}
		else {
			$this->response->redirect($this->url->link('account/login', '', 'SSL'));
		}
	}
}