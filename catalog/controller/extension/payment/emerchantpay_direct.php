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

if (!class_exists('ControllerExtensionPaymentEmerchantPayBase')) {
	require_once DIR_APPLICATION . "controller/extension/payment/emerchantpay/base_controller.php";
}

/**
 * Front-end controller for the "emerchantpay Direct" module
 *
 * @package EMerchantPayDirect
 */
class ControllerExtensionPaymentEmerchantPayDirect extends ControllerExtensionPaymentEmerchantPayBase
{

	/**
	 * Module Name
	 *
	 * @var string
	 */
	protected $module_name = 'emerchantpay_direct';

	/**
	 * Init
	 *
	 * @param $registry
	 */
	public function __construct($registry)
	{
		parent::__construct($registry);
	}

	/**
	 * Entry-point
	 *
	 * @return mixed
	 */
	public function index()
	{
		$this->load->language('extension/payment/emerchantpay_direct');
		$this->load->model('extension/payment/emerchantpay_direct');

		if ($this->model_extension_payment_emerchantpay_direct->isCartContentMixed()) {
			$template = 'emerchantpay_disabled.tpl';
			$data = $this->prepareViewDataMixedCart();

		} else {
			$template = 'emerchantpay_direct.tpl';
			$this->document->addScript(
				'catalog/view/javascript/emerchantpay/card.min.js'
			);
			$data = $this->prepareViewData();
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/' . $template)) {
			return $this->load->view(
				$this->config->get('config_template') . '/template/payment/' . $template,
				$data
			);
		} else {
			return $this->load->view('payment/' . $template, $data);
		}
	}

	/**
	 * Prepares data for the view
	 *
	 * @return array
	 */
	public function prepareViewData()
	{
		$data = array(
			'text_credit_card' => $this->language->get('text_credit_card'),
			'text_loading' => $this->language->get('text_loading'),
			'text_card_legal' => $this->getLegalText(),

			'entry_cc_number' => $this->language->get('entry_cc_number'),
			'entry_cc_owner' => $this->language->get('entry_cc_owner'),
			'entry_cc_expiry' => $this->language->get('entry_cc_expiry'),
			'entry_cc_cvv' => $this->language->get('entry_cc_cvv'),

			'button_confirm' => $this->language->get('button_confirm'),
			'button_target' => $this->url->link('extension/payment/emerchantpay_direct/send', '', 'SSL'),

			'scripts' => $this->document->getScripts(),

			'years' => array(),
			'months' => array()
		);

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

		return $data;
	}

	/**
	 * Prepares data for the view when cart content is mixed
	 *
	 * @return array
	 */
	public function prepareViewDataMixedCart()
	{
		$data = array(
			'text_loading' => $this->language->get('text_loading'),
			'text_payment_mixed_cart_content' => $this->language->get('text_payment_mixed_cart_content'),
			'button_shopping_cart' => $this->language->get('button_shopping_cart'),
			'button_target' => $this->url->link('checkout/cart')
		);

		return $data;
	}

	/**
	 * Process order confirmation
	 *
	 * @return void
	 */
	public function send()
	{
		$this->load->model('checkout/order');
		$this->load->model('extension/payment/emerchantpay_direct');

		$this->load->language('extension/payment/emerchantpay_direct');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		try {

			$data = array(
				'transaction_id' => $this->model_extension_payment_emerchantpay_direct->genTransactionId(self::PLATFORM_TRANSACTION_PREFIX),

				'remote_address' => $this->request->server['REMOTE_ADDR'],

				'usage' => $this->model_extension_payment_emerchantpay_direct->getUsage(),
				'description' => $this->model_extension_payment_emerchantpay_direct->getOrderProducts(
					$this->session->data['order_id']
				),

				'currency' => $this->model_extension_payment_emerchantpay_direct->getCurrencyCode(),
				'amount' => $order_info['total'],

				'customer_email' => $order_info['email'],
				'customer_phone' => $order_info['telephone'],

				'card_holder' => $this->inputFilter(
					$this->request->post['emerchantpay_direct-cc-holder'],
					'name'
				),
				'card_number' => $this->inputFilter(
					$this->request->post['emerchantpay_direct-cc-number'],
					'number'
				),
				'cvv' => $this->inputFilter(
					$this->request->post['emerchantpay_direct-cc-cvv'],
					'cvv'
				),
				'expiration_month' => $this->inputFilter(
					$this->request->post['emerchantpay_direct-cc-expiration'],
					'month'
				),
				'expiration_year' => $this->inputFilter(
					$this->request->post['emerchantpay_direct-cc-expiration'],
					'year'
				),

				'notification_url' => $this->url->link('extension/payment/emerchantpay_direct/callback', '', 'SSL'),
				'return_success_url' => $this->url->link('extension/payment/emerchantpay_direct/success', '', 'SSL'),
				'return_failure_url' => $this->url->link('extension/payment/emerchantpay_direct/failure', '', 'SSL'),

				'billing' => array(
					'first_name' => $order_info['payment_firstname'],
					'last_name' => $order_info['payment_lastname'],
					'address1' => $order_info['payment_address_1'],
					'address2' => $order_info['payment_address_2'],
					'zip' => $order_info['payment_postcode'],
					'city' => $order_info['payment_city'],
					'state' => $order_info['payment_zone_code'],
					'country' => $order_info['payment_iso_code_2'],
				),

				'shipping' => array(
					'first_name' => $order_info['shipping_firstname'],
					'last_name' => $order_info['shipping_lastname'],
					'address1' => $order_info['shipping_address_1'],
					'address2' => $order_info['shipping_address_2'],
					'zip' => $order_info['shipping_postcode'],
					'city' => $order_info['shipping_city'],
					'state' => $order_info['shipping_zone_code'],
					'country' => $order_info['shipping_iso_code_2'],
				)
			);

			$transaction = $this->model_extension_payment_emerchantpay_direct->sendTransaction($data);

			if (isset($transaction->unique_id)) {
				$timestamp = ($transaction->timestamp instanceof \DateTime) ? $transaction->timestamp->format('c') : $transaction->timestamp;

				$data = array(
					'reference_id' => '0',
					'order_id' => $order_info['order_id'],
					'unique_id' => $transaction->unique_id,
					'type' => $transaction->transaction_type,
					'status' => $transaction->status,
					'message' => $transaction->message,
					'technical_message' => $transaction->technical_message,
					'amount' => $transaction->amount,
					'currency' => $transaction->currency,
					'timestamp' => $timestamp,
				);

				$this->model_extension_payment_emerchantpay_direct->populateTransaction($data);

				$redirect_url = $this->url->link('checkout/success', '', 'SSL');

				switch ($transaction->status) {
					case \Genesis\API\Constants\Transaction\States::PENDING_ASYNC:
						$this->model_checkout_order->addOrderHistory(
							$this->session->data['order_id'],
							$this->config->get('emerchantpay_direct_async_order_status_id'),
							$this->language->get('text_payment_status_init_async'),
							true
						);

						if (isset($transaction->threeds_method_continue_url)) {
							throw new \Exception(
								$this->language->get('text_payment_3ds_v2_error')
							);
						}

						if (isset($transaction->redirect_url)) {
							$redirect_url = $transaction->redirect_url;
						}

						break;
					case \Genesis\API\Constants\Transaction\States::APPROVED:
						$this->model_checkout_order->addOrderHistory(
							$this->session->data['order_id'],
							$this->config->get('emerchantpay_direct_order_status_id'),
							$this->language->get('text_payment_status_successful'),
							false
						);

						break;
					case \Genesis\API\Constants\Transaction\States::DECLINED:
					case \Genesis\API\Constants\Transaction\States::ERROR:
						$this->model_checkout_order->addOrderHistory(
							$this->session->data['order_id'],
							$this->config->get('emerchantpay_direct_order_failure_status_id'),
							$this->language->get('text_payment_status_unsuccessful'),
							true
						);

						throw new \Exception(
							$transaction->message
						);

						break;
				}

				if ($this->model_extension_payment_emerchantpay_direct->isRecurringOrder()) {
					$this->addOrderRecurring($transaction->unique_id);
					$this->model_extension_payment_emerchantpay_direct->populateRecurringTransaction($data);
					$this->model_extension_payment_emerchantpay_direct->updateOrderRecurring($data);
				}

				$json = array(
					'redirect' => $redirect_url
				);
			} else {
				$json = array(
					'error' => $this->language->get('text_payment_system_error')
				);
			}
		} catch (\Exception $exception) {
			$json = array(
				'error' => ($exception->getMessage()) ? $exception->getMessage() : $this->language->get('text_payment_system_error')
			);

			$this->model_extension_payment_emerchantpay_direct->logEx($exception);
		}

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(
			json_encode($json)
		);
	}

	/**
	 * Process Gateway Notification
	 *
	 * @return void
	 */
	public function callback()
	{
		$this->load->model('checkout/order');
		$this->load->model('extension/payment/emerchantpay_direct');

		$this->load->language('extension/payment/emerchantpay_direct');

		try {
			$this->model_extension_payment_emerchantpay_direct->bootstrap();

			$notification = new \Genesis\API\Notification(
				$this->request->post
			);

			if ($notification->isAuthentic()) {
				$notification->initReconciliation();

				$reconcile = $notification->getReconciliationObject();

				if (isset($reconcile->unique_id)) {

					$transaction = $this->model_extension_payment_emerchantpay_direct->getTransactionById($reconcile->unique_id);

					if (isset($transaction['order_id']) && abs((int)$transaction['order_id']) > 0) {

						$timestamp = ($reconcile->timestamp instanceof \DateTime) ? $reconcile->timestamp->format('c') : $reconcile->timestamp;

						$data = array(
							'order_id'          => $transaction['order_id'],
							'unique_id'         => $reconcile->unique_id,
							'type'              => $reconcile->transaction_type,
							'mode'              => $reconcile->mode,
							'status'            => $reconcile->status,
							'currency'          => $reconcile->currency,
							'amount'            => $reconcile->amount,
							'timestamp'         => $timestamp,
							'message'           => isset($reconcile->message) ? $reconcile->message : '',
							'technical_message' => isset($reconcile->technical_message) ? $reconcile->technical_message : '',
						);

						$this->model_extension_payment_emerchantpay_direct->populateTransaction($data);

						switch ($reconcile->status) {
							case \Genesis\API\Constants\Transaction\States::APPROVED:
								$this->model_checkout_order->addOrderHistory(
									$transaction['order_id'],
									$this->config->get('emerchantpay_direct_order_status_id'),
									$this->language->get('text_payment_status_successful')
								);
								break;
							case \Genesis\API\Constants\Transaction\States::DECLINED:
							case \Genesis\API\Constants\Transaction\States::ERROR:
								$this->model_checkout_order->addOrderHistory(
									$transaction['order_id'],
									$this->config->get('emerchantpay_direct_order_failure_status_id'),
									$this->language->get('text_payment_status_unsuccessful')
								);
								break;
						}

						if ($this->model_extension_payment_emerchantpay_direct->isRecurringOrder()) {
							$this->model_extension_payment_emerchantpay_direct->populateRecurringTransaction($data);
							$this->model_extension_payment_emerchantpay_direct->updateOrderRecurring($data);
						}

						$this->response->addHeader('Content-Type: text/xml');

						$this->response->setOutput(
							$notification->generateResponse()
						);
					}
				}
			}
		} catch (\Exception $exception) {
			$this->model_extension_payment_emerchantpay_direct->logEx($exception);
		}
	}

	/**
	 * Handle client redirection for successful status
	 *
	 * @return void
	 */
	public function success()
	{
		$this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
	}

	/**
	 * Handle client redirection for failure status
	 *
	 * @return void
	 */
	public function failure()
	{
		$this->load->language('extension/payment/emerchantpay_direct');

		$this->session->data['error'] = $this->language->get('text_payment_failure');

		$this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
	}

	/**
	 * Sanitize incoming data
	 *
	 * @param string $input Field value
	 * @param string $type Field type
	 *
	 * @return mixed|string
	 */
	protected function inputFilter($input, $type)
	{
		switch ($type) {
			case 'number':
				return str_replace(' ', '', $input);
				break;
			case 'cvv':
				return strval($input);
			case 'year':
				@list(, $year) = explode('/', $input);

				$year = trim($year);

				if (strlen($year) == 2) {
					return sprintf('20%s', $year);
				}

				return substr($year, 0, 4);
				break;
			case 'month':
				@list($month,) = explode('/', $input);

				if ($month) {
					return substr(strval($month), 0, 2);
				}
				break;
		}

		return trim($input);
	}

	/**
	 * Redirect the user (to the login page), if they are not logged-in
	 */
	protected function isUserLoggedIn()
	{
		$is_callback = strpos((string)$this->request->get['route'], 'callback') !== false;

		if (!$this->customer->isLogged() && !$is_callback) {
			$this->response->redirect($this->url->link('account/login', '', 'SSL'));
		}
	}

	/**
	 * Generate a legal text for this store
	 *
	 * @return string
	 */
	protected function getLegalText()
	{
		$store_name = $this->config->get('config_name');

		return sprintf('&copy; %s emerchantpay Ltd.<br/><br/>%s', date('Y'), $store_name);
	}

	/**
	 * Adds recurring order
	 * @param string $payment_reference
	 */
	public function addOrderRecurring($payment_reference)
	{
		$recurring_products = $this->cart->getRecurringProducts();
		if (!empty($recurring_products)) {
			$this->load->model('extension/payment/emerchantpay_direct');
			$this->model_extension_payment_emerchantpay_direct->addOrderRecurring(
				$recurring_products,
				$payment_reference
			);
		}
	}

	/**
	 * Process the cron if the request is local
	 *
	 * @return void
	 */
	public function cron()
	{
		$this->load->model('extension/payment/emerchantpay_direct');
		$this->model_extension_payment_emerchantpay_direct->processRecurringOrders();
	}
}
