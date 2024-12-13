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

use Genesis\Api\Constants\Transaction\Parameters\ScaExemptions;
use Genesis\Api\Constants\Transaction\Parameters\Threeds\V2\Control\ChallengeIndicators;
use Genesis\Api\Constants\Transaction\States;

if (!class_exists('\Genesis\Genesis', false)) {
	include DIR_APPLICATION . '/../admin/model/extension/payment/emerchantpay/genesis/vendor/autoload.php';
}

if (!class_exists('EMerchantPayHelper')) {
	require_once DIR_APPLICATION . "model/extension/payment/emerchantpay/EMerchantPayHelper.php";
}

/**
 * Base Abstract Class for Method Admin Controllers
 *
 * Class ControllerExtensionPaymentEmerchantPayBase
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.LongClassName)
 */
abstract class ControllerExtensionPaymentEmerchantPayBase extends Controller
{
	/**
	 * OpenCart constants
	 * The complete set of constants is defined in the ModelExtensionPaymentEmerchantPayBase class
	 */
	const OC_REC_TXN_CANCELLED = 5;
	const OC_ORD_STATUS_REFUNDED = 11;

	/**
	 * Error storage
	 *
	 * @var array
	 */
	protected $error = array();

	/**
	 * Module Name (Used in View - Templates)
	 *
	 * @var string
	 */
	protected $module_name = null;

	/**
	 * Prefix for route (2.3.x -> 'extension/')
	 *
	 * @var null|string
	 */
	protected $route_prefix = null;

	/**
	 * A watch key list for monitoring the submit errors
	 *
	 * @var array
	 */
	protected $error_field_key_list = array(
		'warning',
		'username',
		'password',
		'transaction_type',
		'order_status',
		'order_async_status',
		'order_failure_status',
		'error_sca_exemption_amount'
	);

	/**
	 * Used to find out if the payment method requires SSL
	 *
	 * @return bool
	 */
	abstract protected function isModuleRequiresSsl();

	/**
	 * ControllerExtensionPaymentEmerchantPayBase constructor.
	 * @param $registry
	 * @throws Exception
	 */
	public function __construct($registry)
	{
		parent::__construct($registry);

		if (is_null($this->module_name)) {
			throw new Exception('Module name not supplied in EMerchantPay controller');
		}

		$this->route_prefix = $this->isVersion23OrAbove() ? "extension/" : "";
	}

	/**
	 * Determines if the controller is loaded from the Backend Order Info Action
	 * Needed for 2.3.x and above
	 *
	 * @return bool
	 */
	protected function isOrderInfoRequest()
	{
		return
			($this->request->server['REQUEST_METHOD'] == 'GET') &&
			($this->request->get['route'] == 'sale/order/info');
	}

	/**
	 * Determines if the controller is called from the Extension Module to install the Payment Module
	 * Needed for 2.3.x and above
	 *
	 * @return bool
	 */
	protected function isInstallRequest()
	{
		return
			($this->request->server['REQUEST_METHOD'] == 'GET') &&
			($this->request->get['route'] == 'extension/extension/payment/install');
	}

	/**
	 * Determines if the controller is called from the Extension Module to uninstall the Payment Module
	 * Needed for 2.3.x and above
	 *
	 * @return bool
	 */
	protected function isUninstallRequest()
	{
		return
			($this->request->server['REQUEST_METHOD'] == 'GET') &&
			($this->request->get['route'] == 'extension/extension/payment/uninstall');
	}

	/**
	 * Determines if the controller is loaded from the Backend Order Panel
	 *   - Displaying Backend Transaction Popup Dialog for Capture, Refund and Void
	 * Needed for 2.3.x and above
	 *
	 * @param array $actions
	 * @return bool
	 */
	protected function isModuleSubActionRequest(array $actions)
	{
		return
			($this->request->server['REQUEST_METHOD'] == 'POST') &&
			($this->request->get['route'] == "extension/payment/{$this->module_name}") &&
			array_key_exists('action', $this->request->get) &&
			in_array($this->request->get['action'], $actions);
	}

	/**
	 * Loads the language Model in the controller
	 *
	 * @return void
	 */
	protected function loadLanguage()
	{
		$this->load->language("extension/payment/{$this->module_name}");
	}

	/**
	 * Loads the Payment Method Model in the controller
	 *
	 * @return void
	 */
	protected function loadPaymentMethodModel()
	{
		$this->load->model("extension/payment/{$this->module_name}");
	}

	/**
	 * Retrieves an instance of the backend method model
	 *
	 * @return Model
	 */
	protected function getModelInstance()
	{
		$method = "model_extension_payment_{$this->module_name}";
		return $this->{$method};
	}

	/**
	 * Entry-point
	 *
	 * @return mixed|void
	 */
	public function index()
	{
		if ($this->isVersion23OrAbove()) {
			if ($this->isInstallRequest()) {
				$this->install();
				return true;
			} elseif ($this->isUninstallRequest()) {
				$this->uninstall();
				return true;
			} elseif ($this->isOrderInfoRequest()) {
				return $this->orderAction();
			} else if ($this->isModuleSubActionRequest(['getModalForm', 'capture', 'refund', 'void'])) {
				$method = $this->request->get['action'];
				call_user_func(array($this, $method));
				return true;
			}
		}

		$this->loadLanguage();
		$this->load->model('setting/setting');

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$this->processPostIndexAction();
		} else {
			$this->processGetIndexAction();
		}
	}

	/**
	 * Get token param
	 *
	 * @return string
	 */
	public function getToken()
	{
		return $this->isVersion30OrAbove() ? $this->session->data['user_token'] : $this->session->data['token'];
	}

	/**
	 * Get token param name
	 *
	 * @return string
	 */
	public function getTokenParam()
	{
		return $this->isVersion30OrAbove() ? 'user_token' : 'token';
	}

	/**
	 * Processes HTTP POST Index action
	 *
	 * @return void
	 */
	protected function processPostIndexAction()
	{
		try {
			if ($this->validate()) {
				$this->model_setting_setting->editSetting($this->module_name, $this->request->post);

				// As from 3.x they changed settings name in db.
				// Save status and sort_order settigns in new format. Other settings are not used from opencart core
				if ($this->isVersion30OrAbove()) {
					$settings = array(
						"payment_{$this->module_name}_status" => $this->request->post["{$this->module_name}_status"],
						"payment_{$this->module_name}_sort_order" => $this->request->post["{$this->module_name}_sort_order"]
					);
					$this->model_setting_setting->editSetting("payment_{$this->module_name}", $settings);
				}

				$json = array(
					'success' => 1,
					'text'    => $this->language->get('text_success'),
				);
			} else {
				$error_message = "";

				foreach ($this->error_field_key_list as $error_field_key)
					if (isset($this->error[$error_field_key]))
						$error_message .= sprintf("<li>%s</li>", $this->error[$error_field_key]);

				$error_message = sprintf("<ul>%s</ul>", $error_message);

				$json = array(
					'success' => 0,
					'text'    => $error_message
				);
			}

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
		catch (\Exception $e) {
			$this->response->addHeader('HTTP/1.0 500 Internal Server Error');
		}
	}

	/**
	 * Processes HTTP GET Index action
	 *
	 * @return void
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	protected function processGetIndexAction()
	{
		$this->addExternalResources(array(
			'treeGrid',
			'bootstrapValidator',
			'bootstrapCheckbox',
			'commonStyleSheet'
		));

		if ($this->isModuleRequiresSsl() && !$this->isSecureConnection()) {
			$this->error['warning'] = $this->language->get('error_https');
		}

		$heading_title = $this->language->get('heading_title');
		$this->document->setTitle($heading_title);
		$this->load->model('localisation/geo_zone');
		$this->load->model('localisation/order_status');
		$this->loadPaymentMethodModel();

		$data = $this->buildLanguagePhrases();

		$data += array(
			'module_version'              => $this->getModelInstance()->getVersion(),
			'geo_zones'                   => $this->model_localisation_geo_zone->getGeoZones(),
			'order_statuses'              => $this->model_localisation_order_status->getOrderStatuses(),
			'transaction_types'           => $this->getModelInstance()->getTransactionTypes(),
			'recurring_transaction_types' => $this->getModelInstance()->getRecurringTransactionTypes(),
			'error_warning'               => isset($this->error['warning']) ? $this->error['warning'] : '',
			'enable_recurring_tab'        => $this->isVersion22OrAbove(),

			// Settings
			"{$this->module_name}_username"                    => $this->getFieldValue("{$this->module_name}_username"),
			"{$this->module_name}_password"                    => $this->getFieldValue("{$this->module_name}_password"),
			"{$this->module_name}_token"                       => $this->getFieldValue("{$this->module_name}_token"),
			"{$this->module_name}_sandbox"                     => $this->getFieldValue("{$this->module_name}_sandbox"),
			"{$this->module_name}_transaction_type"            => $this->getFieldValue("{$this->module_name}_transaction_type"),
			"{$this->module_name}_wpf_tokenization"            => $this->getFieldValue("{$this->module_name}_wpf_tokenization"),
			"{$this->module_name}_total"                       => $this->getFieldValue("{$this->module_name}_total"),
			"{$this->module_name}_order_status_id"             => $this->getFieldValue("{$this->module_name}_order_status_id"),
			"{$this->module_name}_order_failure_status_id"     => $this->getFieldValue("{$this->module_name}_order_failure_status_id"),
			"{$this->module_name}_async_order_status_id"       => $this->getFieldValue("{$this->module_name}_async_order_status_id"),
			"{$this->module_name}_geo_zone_id"                 => $this->getFieldValue("{$this->module_name}_geo_zone_id"),
			"{$this->module_name}_status"                      => $this->getFieldValue("{$this->module_name}_status"),
			"{$this->module_name}_sort_order"                  => $this->getFieldValue("{$this->module_name}_sort_order"),
			"{$this->module_name}_debug"                       => $this->getFieldValue("{$this->module_name}_debug"),
			"{$this->module_name}_supports_partial_capture"    => $this->getFieldValue("{$this->module_name}_supports_partial_capture"),
			"{$this->module_name}_supports_partial_refund"     => $this->getFieldValue("{$this->module_name}_supports_partial_refund"),
			"{$this->module_name}_supports_void"               => $this->getFieldValue("{$this->module_name}_supports_void"),
			"{$this->module_name}_supports_recurring"          => $this->getFieldValue("{$this->module_name}_supports_recurring"),
			"{$this->module_name}_recurring_transaction_type"  => $this->getFieldValue("{$this->module_name}_recurring_transaction_type"),
			"{$this->module_name}_recurring_token"             => $this->getFieldValue("{$this->module_name}_recurring_token"),
			"{$this->module_name}_cron_allowed_ip"             => $this->getFieldValue("{$this->module_name}_cron_allowed_ip"),
			"{$this->module_name}_cron_time_limit"             => $this->getFieldValue("{$this->module_name}_cron_time_limit"),
			"{$this->module_name}_bank_codes"                  => $this->getFieldValue("{$this->module_name}_bank_codes"),
			"{$this->module_name}_threeds_allowed"             => $this->getFieldValue("{$this->module_name}_threeds_allowed"),
			"{$this->module_name}_threeds_challenge_indicator" => $this->getFieldValue("{$this->module_name}_threeds_challenge_indicator"),
			"{$this->module_name}_sca_exemption"               => $this->getFieldValue("{$this->module_name}_sca_exemption"),
			"{$this->module_name}_sca_exemption_amount"        => $this->getFieldValue("{$this->module_name}_sca_exemption_amount"),
			"{$this->module_name}_order_email_create"          => $this->getFieldValue("{$this->module_name}_order_email_create"),
			"{$this->module_name}_order_email_payment_failure" => $this->getFieldValue("{$this->module_name}_order_email_payment_failure"),

			'action' => $this->url->link("{$this->route_prefix}payment/{$this->module_name}", $this->getTokenParam() . '=' . $this->getToken(), 'SSL'),
			'cancel' =>	$this->getPaymentLink($this->getToken()),
			'header'      => $this->load->controller('common/header'),
			'column_left' => $this->load->controller('common/column_left'),
			'footer'      => $this->load->controller('common/footer'),

			'recurring_log_entries'      => $this->getRecurringLog(),
			'cron_last_execution'        => $this->getLastCronExecTime(),
			'cron_last_execution_status' => $this->getCronExecStatus(),

			'module_name' => $this->module_name
		);

		if ($this->module_name == 'emerchantpay_checkout') {
			$data += [
				'bank_codes'                   => $this->getModelInstance()->getBankCodes(),
				'threeds_challenge_indicators' => $this->getModelInstance()->getThreedsChallengeIndicators(),
				'sca_exemptions'               => $this->getModelInstance()->getScaExemptions()
			];
		}

		$default_param_values = array(
			"{$this->module_name}_sandbox"                     => 1,
			"{$this->module_name}_status"                      => 0,
			"{$this->module_name}_debug"                       => 1,
			"{$this->module_name}_supports_partial_capture"    => 1,
			"{$this->module_name}_supports_partial_refund"     => 1,
			"{$this->module_name}_supports_void"               => 1,
			"{$this->module_name}_supports_recurring"          => 0,
			"{$this->module_name}_cron_allowed_ip"             => $this->getServerAddress(),
			"{$this->module_name}_cron_time_limit"             => 25,
			"{$this->module_name}_threeds_allowed"             => 1,
			"{$this->module_name}_threeds_challenge_indicator" => ChallengeIndicators::NO_PREFERENCE,
			"{$this->module_name}_sca_exemption"               => ScaExemptions::EXEMPTION_LOW_RISK,
			"{$this->module_name}_sca_exemption_amount"        => 100,
			"{$this->module_name}_order_email_create"          => 1,
			"{$this->module_name}_order_email_payment_failure" => 1,
		);

		foreach ($default_param_values as $key => $default_value)
			$data[$key] = (is_null($data[$key]) ? $default_value : $data[$key]);

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $this->getTokenParam() . '=' . $this->getToken(), 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_payment'),
			'href' => $this->getPaymentLink($this->getToken())
		);

		$data['breadcrumbs'][] = array(
			'text' => $heading_title,
			'href' => $this->url->link("{$this->route_prefix}payment/{$this->module_name}", $this->getTokenParam() . '=' . $this->getToken(), 'SSL')
		);

		$this->response->setOutput(
			$this->load->view("extension/payment/{$this->module_name}.tpl", $data)
		);
	}

	/**
	 * Builds an array with the language phrases used on the templates
	 *
	 * @return mixed
	 *
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	protected function buildLanguagePhrases()
	{
		$result = array();

		$phrases = array(
			'heading_title',
			'tab_general',
			'tab_recurring',
			'text_edit',
			'text_enabled',
			'text_disabled',
			'text_all_zones',
			'text_yes',
			'text_no',
			'text_success',
			'text_failed',
			'text_select_status',

			'text_log_entry_id',
			'text_log_order_id',
			'text_log_date_time',
			'text_log_status_completed',
			'text_log_rebilled_amount',
			'text_log_recurring_order_id',
			'text_log_status',
			'text_log_btn_show',
			'text_log_btn_hide',

			'entry_username',
			'entry_password',
			'entry_token',
			'entry_sandbox',
			'entry_transaction_type',
			'entry_recurring_transaction_type',
			'entry_recurring_log',
			'entry_recurring_token',
			'entry_cron_time_limit',
			'entry_cron_allowed_ip',
			'entry_cron_last_execution',
			'entry_bank_codes',
			'entry_threeds_allowed',
			'entry_threeds_challenge_indicator',
			'entry_sca_exemption',
			'entry_sca_exemption_value',
			'entry_order_email_create',
			'entry_order_email_payment_failure',

			'entry_order_status',
			'entry_async_order_status',
			'entry_order_status_failure',
			'entry_total',
			'entry_geo_zone',
			'entry_status',
			'entry_debug',
			'entry_sort_order',
			'entry_supports_partial_capture',
			'entry_supports_partial_refund',
			'entry_supports_void',
			'entry_supports_recurring',

			'help_sandbox',
			'help_total',
			'help_order_status',
			'help_async_order_status',
			'help_failure_order_status',
			'help_supports_partial_capture',
			'help_supports_partial_refund',
			'help_supports_void',
			'help_supports_recurring',
			'help_recurring_transaction_types',
			'help_recurring_log',
			'help_cron_time_limit',
			'help_cron_allowed_ip',
			'help_cron_last_execution',
			'help_threeds_allowed',
			'help_threeds_challenge_indicator',
			'help_sca_exemption',
			'help_sca_exemption_value',
			'help_order_email_create',
			'help_order_email_payment_failure',

			'button_save',
			'button_cancel',

			'error_username',
			'error_password',
			'error_token',
			'error_transaction_type',
			'error_controls_invalidated',
			'error_order_status',
			'error_order_failure_status',
			'error_async_order_status',

			'alert_disable_recurring',
		);

		foreach ($phrases as $phrase) {
			$result[$phrase] = $this->language->get($phrase);
		}

		return $result;
	}

	/**
	 * Get transactions list (openCart 2.1.x)
	 *
	 * @return mixed
	 */
	public function order()
	{
		return $this->orderAction();
	}

	/**
	 * Get transactions list
	 *
	 * @return mixed
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function orderAction()
	{
		if ($this->config->get("{$this->module_name}_status")) {

			$this->loadLanguage();

			$this->loadPaymentMethodModel();

			$this->addExternalResources(array(
				'treeGrid',
				'bootstrapValidator',
				'jQueryNumber',
				'commonStyleSheet'
			));

			$order_id = $this->request->get['order_id'];
			$transactions = $this->getModelInstance()->getTransactionsByOrder($this->request->get['order_id']);

			$has_currency_method = method_exists($this->currency, 'has');

			if ($transactions) {
				// Process individual fields
				foreach ($transactions as &$transaction) {
					/* OpenCart 2.2.x Fix (Cart\Currency does not check if the given currency code exists */
					if (($has_currency_method && $this->currency->has($transaction['currency'])) || (!$has_currency_method && !empty($transaction['currency'])))
						$transaction['amount'] = $this->currency->format($transaction['amount'], $transaction['currency']);
					else /* No Currency Code is stored on Void Transaction */
						$transaction['amount'] = "";

					$transaction['timestamp']   = date('H:i:s m/d/Y', strtotime($transaction['timestamp']));
					$transaction['can_capture'] = $this->canCaptureTransaction($transaction);
					$transaction['can_refund']  = $this->canRefundTransaction($transaction);
					$transaction['can_void']    = $this->canVoidTransaction($transaction);
					$transaction['void_exists'] = $this->isVoidTransactionExist($order_id, $transaction);
				}

				// Sort the transactions list in the following order:
				//
				// 1. Sort by timestamp (date), i.e. most-recent transactions on top
				// 2. Sort by relations, i.e. every parent has the child nodes immediately after

				// Ascending Date/Timestamp sorting
				uasort($transactions, function ($element1, $element2) {
					// sort by timestamp (date) first
					if (isset($element1['timestamp']) && isset($element2['timestamp'])) {
						if ($element1['timestamp'] == $element2['timestamp']) {
							return 0;
						}
						return ($element1["timestamp"] > $element2["timestamp"]) ? 1 : -1;
					}
					return -1;
				});

				// Create the parent/child relations from a flat array
				$array_asc = array();

				foreach ($transactions as $key => $val) {
					// create an array with ids as keys and children
					// with the assumption that parents are created earlier.
					// store the original key
					if (isset($array_asc[$val['unique_id']])) {
						$array_asc[$val['unique_id']]['org_key'] = $key;

						$array_asc[$val['unique_id']] = array_merge($val, $array_asc[$val['unique_id']]);
					} else {
						$array_asc[$val['unique_id']] = array_merge($val, array('org_key' => $key));
					}

					if ($val['reference_id']) {
						$array_asc[$val['reference_id']]['children'][] = $val['unique_id'];
					}
				}

				// Order the parent/child entries
				$transactions = array();

				foreach ($array_asc as $val) {
					if (isset($val['reference_id']) && $val['reference_id']) {
						continue;
					}

					$this->sortTransactionByRelation($transactions, $val, $array_asc);
				}

				$data = array(

					'text_payment_info'                              => $this->language->get('text_payment_info'),
					'text_transaction_id'                            => $this->language->get('text_transaction_id'),
					'text_transaction_timestamp'                     => $this->language->get('text_transaction_timestamp'),
					'text_transaction_amount'                        => $this->language->get('text_transaction_amount'),
					'text_transaction_status'                        => $this->language->get('text_transaction_status'),
					'text_transaction_type'                          => $this->language->get('text_transaction_type'),
					'text_transaction_message'                       => $this->language->get('text_transaction_message'),
					'text_transaction_mode'                          => $this->language->get('text_transaction_mode'),
					'text_transaction_action'                        => $this->language->get('text_transaction_action'),

					'help_transaction_option_capture_partial_denied' => $this->language->get('help_transaction_option_capture_partial_denied'),
					'help_transaction_option_refund_partial_denied'  => $this->language->get('help_transaction_option_refund_partial_denied'),
					'help_transaction_option_cancel_denied'          => $this->language->get('help_transaction_option_cancel_denied'),

					"{$this->module_name}_supports_void"             => $this->config->get("{$this->module_name}_supports_void"),
					"{$this->module_name}_supports_recurring"        => $this->config->get("{$this->module_name}_supports_recurring"),

					'order_id'                   => $order_id,
					'token'                      => $this->request->get[$this->getTokenParam()],
					'url_modal'                  => htmlspecialchars_decode($this->getModalFormLink($this->getToken())),
					'module_name'                => $this->module_name,
					'currency'		             => $this->getTemplateCurrencyArray(),
					'transactions'               => $transactions,
				);

				return $this->load->view("extension/payment/{$this->module_name}_order.tpl", $data);
			}
		}

		return false;
	}

	/**
	 * Get transaction's modal form
	 *
	 * @return void
	 */
	public function getModalForm()
	{
		if (isset($this->request->post['reference_id']) && isset($this->request->post['type'])) {
			$this->loadLanguage();
			$this->loadPaymentMethodModel();

			$reference_id = $this->request->post['reference_id'];
			$type         = $this->request->post['type'];
			$order_id     = $this->request->post['order_id'];

			$transaction = $this->getModelInstance()->getTransactionById($reference_id);

			if ($type == 'capture') {
				$total_authorized_amount         = $this->getModelInstance()->getTransactionsSumAmount(
					$order_id,
					$transaction['reference_id'],
					array(
						\Genesis\Api\Constants\Transaction\Types::AUTHORIZE,
						\Genesis\Api\Constants\Transaction\Types::AUTHORIZE_3D,
						\Genesis\Api\Constants\Transaction\Types::GOOGLE_PAY,
						\Genesis\Api\Constants\Transaction\Types::PAY_PAL,
						\Genesis\Api\Constants\Transaction\Types::APPLE_PAY,
					),
					\Genesis\Api\Constants\Transaction\States::APPROVED
				);
				$total_captured_amount           = $this->getModelInstance()->getTransactionsSumAmount($order_id, $transaction['unique_id'], 'capture', 'approved');
				$transaction['available_amount'] = $total_authorized_amount - $total_captured_amount;
			}
			else if ($type == 'refund') {
				$has_void_transaction = $this->getModelInstance()->getTransactionsByTypeAndStatus($order_id, $transaction['unique_id'], 'void', 'approved');
				if (!$has_void_transaction) {
					$total_captured_amount           = $transaction['amount'];
					$total_refunded_amount           = $this->getModelInstance()->getTransactionsSumAmount($order_id, $transaction['unique_id'], 'refund', 'approved');
					$transaction['available_amount'] = $total_captured_amount - $total_refunded_amount;
				}
				else {
					$transaction['available_amount'] = 0;
				}
			}
			else if ($type == 'void') {
				$transaction['is_allowed'] = $this->getModelInstance()->getTransactionsByTypeAndStatus($order_id, $transaction['unique_id'], 'void', 'approved') == false;
			}

			if ($this->isVersion23OrAbove()) {
				$url_action = $this->url->link(
					"extension/payment/{$this->module_name}", "action={$type}&{$this->getTokenParam()}={$this->getToken()}", 'SSL'
				);
			} else {
				$url_action = $this->url->link(
					"payment/{$this->module_name}/{$type}", "token={$this->session->data['token']}", 'SSL'
				);
			}

			$data = array(
				'type'                        => $type,
				'transaction'                 => $transaction,
				'currency'					  => $this->getTemplateCurrencyArray(),
				'url_action'                  => $url_action,
				'module_name'				  => $this->module_name,

				'text_button_close'           => $this->language->get('text_button_close'),
				'text_button_capture_partial' => $this->language->get('text_button_capture_partial'),
				'text_button_capture_full'    => $this->language->get('text_button_capture_full'),
				'text_button_refund_partial'  => $this->language->get('text_button_refund_partial'),
				'text_button_refund_full'     => $this->language->get('text_button_refund_full'),
				'text_button_void'            => $this->language->get('text_button_void'),

				'text_modal_title_capture'    => $this->language->get('text_modal_title_capture'),
				'text_modal_title_refund'     => $this->language->get('text_modal_title_refund'),
				'text_modal_title_void'       => $this->language->get('text_modal_title_void'),

				'help_transaction_option_capture_partial_denied' => $this->language->get('help_transaction_option_capture_partial_denied'),
				'help_transaction_option_refund_partial_denied'  => $this->language->get('help_transaction_option_refund_partial_denied'),
				'help_transaction_option_cancel_denied'          => $this->language->get('help_transaction_option_cancel_denied'),

				"{$this->module_name}_supports_partial_capture"   => $this->config->get("{$this->module_name}_supports_partial_capture"),
				"{$this->module_name}_supports_partial_refund"    => $this->config->get("{$this->module_name}_supports_partial_refund"),
				"{$this->module_name}_supports_void"              => $this->config->get("{$this->module_name}_supports_void"),
				"{$this->module_name}_supports_recurring"         => $this->config->get("{$this->module_name}_supports_recurring")
			);

			$this->response->setOutput($this->load->view("extension/payment/{$this->module_name}_order_modal.tpl", $data));
		}
	}

	/**
	 * Perform a Capture transaction
	 *
	 * @return void
	 */
	public function capture()
	{
		$this->loadLanguage();

		if (isset($this->request->post['reference_id']) && trim($this->request->post['reference_id']) != '') {
			$this->loadPaymentMethodModel();

			$transaction = $this->getModelInstance()->getTransactionById($this->request->post['reference_id']);

			$terminal_token =
				array_key_exists('terminal_token', $transaction) ? $transaction['terminal_token'] : null;

			if (isset($transaction['order_id']) && abs((int)$transaction['order_id']) > 0) {
				$amount  = $this->request->post['amount'];
				$message = isset($this->request->post['message']) ? $this->request->post['message'] : '';
				$capture = $this->getModelInstance()->capture(
					$transaction['type'],
					$transaction['unique_id'],
					$amount,
					$transaction['currency'],
					empty($message) ? 'Capture Opencart Transaction' : $message,
					$transaction['order_id'],
					$terminal_token
				);

				if (isset($capture->unique_id)) {
					$timestamp = ($capture->timestamp instanceof \DateTime) ? $capture->timestamp->format('c') : $capture->timestamp;

					$data = array(
						'order_id'          => $transaction['order_id'],
						'reference_id'      => $transaction['unique_id'],
						'unique_id'         => $capture->unique_id,
						'type'              => $capture->transaction_type,
						'status'            => $capture->status,
						'amount'            => $capture->amount,
						'currency'          => $capture->currency,
						'timestamp'         => $timestamp,
						'message'           => isset($capture->message) ? $capture->message : '',
						'technical_message' => isset($capture->technical_message) ? $capture->technical_message : '',
					);

					if (array_key_exists('terminal_token', $transaction)) {
						$data['terminal_token'] = $transaction['terminal_token'];
					} elseif (isset($capture->terminal_token)) {
						$data['terminal_token'] = $capture->terminal_token;
					}

					$this->getModelInstance()->populateTransaction($data);

					$json = array(
						'error' => false,
						'text'  => isset($capture->message) ? $capture->message : $this->language->get('text_response_success')
					);
				} else {
					$json = array(
						'error' => true,
						'text'  => isset($capture->message) ? $capture->message : $this->language->get('text_response_failure')
					);
				}
			} else {
				$json = array(
					'error' => true,
					'text'  => $this->language->get('text_invalid_reference_id'),
				);
			}
		} else {
			$json = array(
				'error' => true,
				'text'  => $this->language->get('text_invalid_request')
			);
		}

		if (isset($json['error']) && $json['error']) {
			$this->response->addHeader('HTTP/1.0 500 Internal Server Error');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Perform a Refund transaction
	 *
	 * @return void
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function refund()
	{
		$this->loadLanguage();

		if (isset($this->request->post['reference_id']) && trim($this->request->post['reference_id']) != '') {
			$this->loadPaymentMethodModel();

			$transaction = $this->getModelInstance()->getTransactionById($this->request->post['reference_id']);

			$terminal_token =
				array_key_exists('terminal_token', $transaction) ? $transaction['terminal_token'] : null;

			if (isset($transaction['order_id']) && intval($transaction['order_id']) > 0) {
				$amount  = $this->request->post['amount'];
				$message = isset($this->request->post['message']) ? $this->request->post['message'] : '';
				$refund  = $this->getModelInstance()->refund(
					$transaction['type'],
					$transaction['unique_id'],
					$amount,
					$transaction['currency'],
					empty($message) ? 'Refund Opencart Transaction' : $message,
					$terminal_token,
					$transaction['order_id']
				);

				if (isset($refund->unique_id)) {
					$timestamp = ($refund->timestamp instanceof \DateTime) ? $refund->timestamp->format('c') : $refund->timestamp;

					$data = array(
						'order_id'          => $transaction['order_id'],
						'reference_id'      => $transaction['unique_id'],
						'unique_id'         => $refund->unique_id,
						'type'              => $refund->transaction_type,
						'status'            => $refund->status,
						'amount'            => $refund->amount,
						'currency'          => $refund->currency,
						'timestamp'         => $timestamp,
						'message'           => isset($refund->message) ? $refund->message : '',
						'technical_message' => isset($refund->technical_message) ? $refund->technical_message : '',
					);

					if (array_key_exists('terminal_token', $transaction)) {
						$data['terminal_token'] = $transaction['terminal_token'];
					} elseif (isset($refund->terminal_token)) {
						$data['terminal_token'] = $refund->terminal_token;
					}

					$this->getModelInstance()->populateTransaction($data);

					if ($this->isInitialRecurringTransaction($transaction['type'])) {
						$total_captured_amount = $transaction['amount'];
						$total_refunded_amount = $this->getModelInstance()->getTransactionsSumAmount($transaction['order_id'], $transaction['unique_id'], 'refund', 'approved');
						if ($total_captured_amount == $total_refunded_amount) {//is fully refunded?
							$this->cancelOrderRecurring($transaction);

							// Create 'Cancelled' recurring order transaction with the total refunded amount
							$oc_txn_type = self::OC_REC_TXN_CANCELLED;
							$data['amount'] = $total_refunded_amount;
							$this->addRecurringTransaction($data, $oc_txn_type);

							// Update order status to 'Refunded'
							$order_status_id = self::OC_ORD_STATUS_REFUNDED;
							$this->updateOrder(
								$transaction['order_id'],
								$order_status_id,
								$this->language->get('text_recurring_fully_refunded'),
								false
							);
						}
					}

					$json = array(
						'error' => false,
						'text'  => isset($refund->message) ? $refund->message : $this->language->get('text_response_success')
					);
				} else {
					$json = array(
						'error' => true,
						'text'  => isset($refund->message) ? $refund->message : $this->language->get('text_response_failure')
					);
				}
			} else {
				$json = array(
					'error' => true,
					'text'  => $this->language->get('text_invalid_reference_id'),
				);
			}
		} else {
			$json = array(
				'error' => true,
				'text'  => $this->language->get('text_invalid_request')
			);
		}

		if (isset($json['error']) && $json['error']) {
			$this->response->addHeader('HTTP/1.0 500 Internal Server Error');
		}

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(
			json_encode($json)
		);
	}

	/**
	 * Perform a Void transaction
	 *
	 * @return void
	 */
	public function void()
	{
		$this->loadLanguage();

		if (isset($this->request->post['reference_id']) && trim($this->request->post['reference_id']) != '') {
			$this->loadPaymentMethodModel();

			$transaction = $this->getModelInstance()->getTransactionById($this->request->post['reference_id']);

			$terminal_token =
				array_key_exists('terminal_token', $transaction) ? $transaction['terminal_token'] : null;

			if (isset($transaction['order_id']) && abs((int)$transaction['order_id']) > 0) {
				$message = isset($this->request->post['message']) ? $this->request->post['message'] : '';

				$void = $this->getModelInstance()->void(
					$transaction['unique_id'],
					empty($message) ? 'Void Opencart Transaction' : $message,
					$terminal_token
				);

				if (isset($void->unique_id)) {
					$timestamp = ($void->timestamp instanceof \DateTime) ? $void->timestamp->format('c') : $void->timestamp;

					$data = array(
						'order_id'          => $transaction['order_id'],
						'reference_id'      => $transaction['unique_id'],
						'unique_id'         => $void->unique_id,
						'type'              => $void->transaction_type,
						'status'            => $void->status,
						'timestamp'         => $timestamp,
						'message'           => isset($void->message) ? $void->message : '',
						'technical_message' => isset($void->technical_message) ? $void->technical_message : '',
					);

					if (array_key_exists('terminal_token', $transaction)) {
						$data['terminal_token'] = $transaction['terminal_token'];
					} elseif (isset($void->terminal_token)) {
						$data['terminal_token'] = $void->terminal_token;
					}

					$this->getModelInstance()->populateTransaction($data);

					$json = array(
						'error' => false,
						'text'  => isset($void->message) ? $void->message : $this->language->get('text_response_success')
					);
				} else {
					$json = array(
						'error' => true,
						'text'  => isset($void->message) ? $void->message : $this->language->get('text_response_failure')
					);
				}
			} else {
				$json = array(
					'error' => true,
					'text'  => $this->language->get('text_invalid_reference_id'),
				);
			}
		} else {
			$json = array(
				'error' => true,
				'text'  => $this->language->get('text_invalid_request')
			);
		}

		// Add 500 header to trigger jQuery's AJAX Error handling
		if (isset($json['error']) && $json['error']) {
			$this->response->addHeader('HTTP/1.0 500 Internal Server Error');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Add/Install Module Handling
	 *
	 * @return void
	 */
	public function install()
	{
		$this->loadPaymentMethodModel();
		$this->getModelInstance()->install();

		// Attach events for admin and catalog views
		if ($this->isVersion30OrAbove()) {
			$this->load->model('setting/event');
			$this->model_setting_event->addEvent("payment_{$this->module_name}", 'admin/view/*/before', "extension/payment/{$this->module_name}/overrideTemplateEngine", 1);
			$this->model_setting_event->addEvent("payment_{$this->module_name}", 'catalog/view/*/before', "extension/payment/{$this->module_name}/overrideTemplateEngine", 1);
			$this->model_setting_event->addEvent("payment_{$this->module_name}", 'admin/view/*/after', "extension/payment/{$this->module_name}/revertTemplateEngine", 1);
			$this->model_setting_event->addEvent("payment_{$this->module_name}", 'catalog/view/*/after', "extension/payment/{$this->module_name}/revertTemplateEngine", 1);
		}

		// Attach event for catalog model
		if ($this->isVersion23OrAbove() && !$this->isVersion30OrAbove()) {
			$this->load->model('extension/event');
			$this->model_extension_event->addEvent("payment_{$this->module_name}", "catalog/model/extension/payment/{$this->module_name}/*/before", "extension/payment/{$this->module_name}/overrideModelRoute", 1);
		}
	}

	/**
	 * Event handler for admin/view/*\/before
	 * Switch to tpl template engine
	 *
	 * @param $route
	 */
	public function overrideTemplateEngine(&$route) {
		if (strpos($route, $this->module_name)) {

			// save original template engine
			if (!$this->org_template_engine) {
				$this->org_template_engine = $this->config->get('template_engine');
			}

			// remove file extension as it is added later in template engine
			$route = preg_replace('/tpl$/', '', $route);

			$this->config->set('template_engine', 'template');
		}
	}

	/**
	 * Event handler for admin/view/* /after
	 * Switch back to original template engine
	 *
	 * @param $route
	 */
	public function revertTemplateEngine(&$route) {
		if (strpos($route, $this->module_name)) {
			if ($this->org_template_engine) {
				$this->config->set('template_engine', $this->org_template_engine);
			}
		}
	}

	/**
	 * Remove/Uninstall Module Handling
	 *
	 * @return void
	 */
	public function uninstall()
	{
		$this->loadPaymentMethodModel();
		$this->getModelInstance()->uninstall();

		// delete events
		if ($this->isVersion30OrAbove()) {
			$this->load->model('setting/event');
			$this->model_setting_event->deleteEventByCode("payment_{$this->module_name}");
		} else if ($this->isVersion23OrAbove()) {
			$this->load->model('extension/event');
			$this->model_extension_event->deleteEvent("payment_{$this->module_name}");
		}
	}

	/**
	 * Ensure that the current user has permissions to see/modify this module
	 *
	 * @return bool
	 */
	protected function validate()
	{
		if (!$this->user->hasPermission('modify', "{$this->route_prefix}payment/{$this->module_name}")) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (empty($this->request->post["{$this->module_name}_username"])) {
			$this->error['username'] = $this->language->get('error_username');
		}

		if (empty($this->request->post["{$this->module_name}_password"])) {
			$this->error['password'] = $this->language->get('error_password');
		}

		if (empty($this->request->post["{$this->module_name}_transaction_type"])) {
			$this->error['transaction_type'] = $this->language->get('error_transaction_type');
		}

		if (empty($this->request->post["{$this->module_name}_order_status_id"])) {
			$this->error['order_status'] = $this->language->get('error_order_status');
		}

		if (empty($this->request->post["{$this->module_name}_order_failure_status_id"])) {
			$this->error['order_failure_status'] = $this->language->get('error_order_failure_status');
		}

		if ($this->module_name === 'emerchantpay_checkout' && ((float)$this->request->post["{$this->module_name}_sca_exemption_amount"] < 0)) {
			$this->error['error_sca_exemption_amount'] = $this->language->get('error_sca_exemption_amount');
		}

		return !$this->error;
	}

	/**
	 * Check if there's a POST parameter or use the existing configuration value
	 *
	 * @param $key string
	 *
	 * @return mixed
	 */
	protected function getFieldValue($key)
	{
		if (isset($this->request->post[$key])) {
			return $this->request->post[$key];
		}

		return $this->config->get($key);
	}

	/**
	 * Check if the the current visitor is logged in and has permission to access
	 * this page
	 */
	protected function isUserLoggedInAndAuthorized()
	{
		$is_logged_in = $this->user->isLogged();

		$has_access  = $this->user->hasPermission('access', "{$this->route_prefix}payment/{$this->module_name}");

		if (!$is_logged_in || !$has_access) {
			$this->response->redirect(
				$this->url->link('account/login', '', 'SSL')
			);
		}
	}

	/**
	 * Check if the current visitor is on HTTPS
	 *
	 * @return bool
	 */
	protected function isSecureConnection()
	{
		if (!empty($this->request->server['HTTPS']) && strtolower($this->request->server['HTTPS']) != 'off') {
			return true;
		}

		if (!empty($this->request->server['HTTP_X_FORWARDED_PROTO']) && $this->request->server['HTTP_X_FORWARDED_PROTO'] == 'https') {
			return true;
		}

		if (!empty($this->request->server['HTTP_X_FORWARDED_PORT']) && $this->request->server['HTTP_X_FORWARDED_PORT'] == '443') {
			return true;
		}

		return false;
	}

	/**
	 * Recursive function used in the process of sorting
	 * the Transactions list
	 *
	 * @param $array_out array
	 * @param $val array
	 * @param $array_asc array
	 */
	protected function sortTransactionByRelation(&$array_out, $val, $array_asc)
	{
		if (isset($val['org_key'])) {
			$array_out[$val['org_key']] = $val;

			if (isset($val['children']) && sizeof($val['children'])) {
				foreach ($val['children'] as $id) {
					$this->sortTransactionByRelation($array_out, $array_asc[$id], $array_asc);
				}
			}

			unset($array_out[$val['org_key']]['children'], $array_out[$val['org_key']]['org_key']);
		}
	}

	/**
	 * Get current Currency Code
	 * @return string
	 */
	protected function getCurrencyCode() {
		return array_key_exists('currency', $this->session->data) ? $this->session->data['currency'] : '';
	}

	/**
	 * Creates an array from a currency code, in order to be given to the template
	 * @param string $currency_code
	 * @return array
	 */
	protected function getTemplateCurrencyArray($currency_code = null) {
		if (empty($currency_code))
			$currency_code = $this->getCurrencyCode();

		$this->load->model('localisation/currency');
		$currency = $this->model_localisation_currency->getCurrencyByCode($currency_code);

		$currency['symbol_left']  = array_key_exists('symbol_left', $currency) ? $currency['symbol_left'] : '';
		$currency['symbol_right'] = array_key_exists('symbol_right', $currency) ? $currency['symbol_right'] : '';
		$currency['code']         = array_key_exists('code', $currency) ? $currency['code'] : '';

		$currency_symbol = (!empty($currency['symbol_left'])) ? $currency['symbol_left'] : $currency['symbol_right'];
		if (empty($currency_symbol))
			$currency_symbol = $currency['code'];

		return $currency = array(
			'iso_code' 		    => $currency['code'],
			'sign' 		 		=> $currency_symbol,
			'decimalPlaces' 	=> 2,
			'decimalSeparator'  => '.',
			'thousandSeparator' => '' /* must be empty, otherwise exception could be thrown from Genesis */
		);
	}

	/**
	 * Add External Resources (JS & CSS)
	 *
	 * @param $resource_names array
	 *
	 * @return bool
	 */
	protected function addExternalResources($resource_names) {
		$resources_loaded = (bool)count($resource_names) > 0;

		foreach ($resource_names as $resource_name)
			$resources_loaded = $this->addExternalResource($resource_name) && $resources_loaded;

		return $resources_loaded;
	}

	/**
	 * Add External Resource (JS & CSS)
	 *
	 * @param $resource_name string
	 *
	 * @return bool
	 */
	protected function addExternalResource($resource_name) {
		$resource_loaded = true;

		if ($resource_name == 'treeGrid') {
			$this->document->addStyle('view/javascript/emerchantpay/treegrid/css/jquery.treegrid.css');
			$this->document->addScript('view/javascript/emerchantpay/treegrid/js/jquery.treegrid.js');
			$this->document->addScript('view/javascript/emerchantpay/treegrid/js/jquery.treegrid.bootstrap3.js');
		}
		else if ($resource_name == 'bootstrapValidator') {
			$this->document->addStyle('view/javascript/emerchantpay/bootstrap/css/bootstrapValidator.min.css');
			$this->document->addScript('view/javascript/emerchantpay/bootstrap/js/bootstrapValidator.min.js');
		}
		else if ($resource_name == 'bootstrapCheckbox') {
			$this->document->addScript('view/javascript/emerchantpay/bootstrap/js/bootstrap-checkbox.min.js');
		}
		else if ($resource_name == 'jQueryNumber') {
			$this->document->addScript('view/javascript/emerchantpay/jQueryExtensions/js/jquery.number.min.js');
		}
		else if ($resource_name == 'commonStyleSheet') {
			$this->document->addStyle('view/stylesheet/emerchantpay/emerchantpay-admin.css');
		}else
			$resource_loaded = false;

		return $resource_loaded;
	}

	/**
	 * Determines if the OpenCart Version is 2.2.x.x or above
	 * Used to show/hide the tab containing the recurring settings
	 *
	 * @return bool
	 */
	protected function isVersion22OrAbove()
	{
		return defined('VERSION') && version_compare(VERSION, '2.2', '>=');
	}

	/**
	 * Determines if the OpenCart Version is 2.3.x.x or above
	 * Used to build the links in a different way
	 *
	 * @return bool
	 */
	protected function isVersion23OrAbove()
	{
		return defined('VERSION') && version_compare(VERSION, '2.3', '>=');
	}

	/**
	 * Determines if the OpenCart Version is 3.0.x.x or above
	 * Used to build the links in a different way
	 *
	 * @return bool
	 */
	protected function isVersion30OrAbove()
	{
		return defined('VERSION') && version_compare(VERSION, '3.0', '>=');
	}

	/**
	 * Cancels order recurring
	 * @param array $data
	 * @return bool
	 */
	public function cancelOrderRecurring($data)
	{
		$recurring_status = 3; //Cancelled

		$order_recurring_id = $this->getOrderRecurringId($data['order_id']);

		$this->db->query("UPDATE " . DB_PREFIX . "order_recurring SET status = '" . (int)$recurring_status . "'" . " WHERE order_recurring_id = '" . (int)$order_recurring_id . "'");
		return ($this->db->countAffected() > 0);
	}

	/**
	 * Gets order recurring id by order id
	 * @param string $order_id
	 * @return string
	 */
	public function getOrderRecurringId($order_id)
	{
		$query = $this->db->query("SELECT order_recurring_id FROM `" . DB_PREFIX . "order_recurring` WHERE order_id = '" . (int)$order_id . "'");
		return $query->row['order_recurring_id'];
	}

	/**
	 * Is transaction INIT_RECURRING_SALE or INIT_RECURRING_SALE_3D?
	 *
	 * @param const $transaction_type
	 * @return bool
	 */
	public function isInitialRecurringTransaction($transaction_type)
	{
		return in_array($transaction_type, array(
			\Genesis\Api\Constants\Transaction\Types::INIT_RECURRING_SALE,
			\Genesis\Api\Constants\Transaction\Types::INIT_RECURRING_SALE_3D
		));
	}


	/**
	 * Adds recurring transaction to the DB table order_recurring_transaction
	 * @param array $data
	 * @param int $oc_txn_type
	 * @return bool
	 */
	public function addRecurringTransaction($data, $oc_txn_type)
	{
		$result = false;

		if (!array_key_exists('order_recurring_id', $data)) {
			$data['order_recurring_id'] = $this->getOrderRecurringId($data['order_id']);
		}
		if (!empty($data['order_recurring_id'])) {
			$result = $this->db->query("INSERT INTO `" . DB_PREFIX . "order_recurring_transaction` SET `order_recurring_id` = '" . (int)$data['order_recurring_id'] . "', `reference` = '" . $data['unique_id'] . "', `type` = '" . $oc_txn_type . "', `amount` = '" . $data['amount'] . "', `date_added` = NOW()");
		}

		return $result;
	}

	/**
	 * Updates the order and adds it to the order history
	 * @param string $order_id
	 * @param string $order_status_id
	 * @param string $comment
	 * @param bool $notify
	 * @return bool
	 */
	public function updateOrder($order_id, $order_status_id, $comment = '', $notify = false)
	{
		$result = $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$order_status_id . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");
		if ($result) {
			$result = $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
		}
		return $result;
	}

	/**
	 * Can Capture Transaction
	 *
	 * @param array $transaction
	 * @return bool
	 */
	public function canCaptureTransaction($transaction)
	{
		if (!$this->hasApprovedState($transaction['status'])) {
			return false;
		}

		if ($this->isTransactionWithCustomAttribute($transaction['type'])) {
			return $this->checkReferenceActionByCustomAttr(
				EMerchantPayHelper::REFERENCE_ACTION_CAPTURE,
				$transaction['type']
			);
		}

		return \Genesis\Api\Constants\Transaction\Types::canCapture($transaction['type']);
	}

	/**
	 * Can Refund Transaction
	 *
	 * @param array $transaction
	 * @return bool
	 */
	public function canRefundTransaction($transaction)
	{
		if (!$this->hasApprovedState($transaction['status'])) {
			return false;
		}

		if ($this->isTransactionWithCustomAttribute($transaction['type'])) {
			return $this->checkReferenceActionByCustomAttr(
				EMerchantPayHelper::REFERENCE_ACTION_REFUND,
				$transaction['type']
			);
		}

		return \Genesis\Api\Constants\Transaction\Types::canRefund($transaction['type']);
	}

	/**
	 * Can Void Transaction
	 *
	 * @param array $transaction
	 * @return bool
	 */
	public function canVoidTransaction($transaction)
	{
		return \Genesis\Api\Constants\Transaction\Types::canVoid($transaction['type']) &&
			$this->hasApprovedState($transaction['status']);
	}

	/**
	 * Is approved Void transaction exist
	 *
	 * @param string $order_id
	 * @param array $transaction
	 * @return bool
	 */
	public function isVoidTransactionExist($order_id, $transaction)
	{
		return $this->getModelInstance()->getTransactionsByTypeAndStatus(
			$order_id,
			$transaction['unique_id'],
			\Genesis\Api\Constants\Transaction\Types::VOID,
			\Genesis\Api\Constants\Transaction\States::APPROVED
		) !== false;
	}

	/**
	 * Gets the content for generating the recurring log table
	 *
	 * @return array two-dimensional array where the first element is the index of the log/transaction entry
	 * and the second one is an array containing the following elements used in the respective templates:
	 *
	 * log_entry_id
	 * ref_log_entry_id
	 * order_id
	 * order_link
	 * order_link_title
	 * date
	 * amount
	 * order_recurring_id
	 * order_recurring_btn_link
	 * order_recurring_btn_title
	 * status
	 */
	public function getRecurringLog()
	{
		$result = array();

		$query = $this->db->query('SELECT *, ort.`date_added` as `transaction_date` FROM `' . DB_PREFIX . $this->module_name . '_cronlog` '
			. 'JOIN `' . DB_PREFIX . $this->module_name . '_cronlog_transactions` USING(`log_entry_id`) '
			. 'JOIN `' . DB_PREFIX . 'order_recurring_transaction` as ort USING(`order_recurring_transaction_id`) '
			. 'JOIN `' . DB_PREFIX . 'order` USING(`order_id`) '
			. 'ORDER BY `log_entry_id` DESC,`order_recurring_transaction_id` DESC ');

		if ($query->num_rows) {
			$log_entry_line = null;
			$report_line = 0;
			$tmp = array();
			foreach ($query->rows as $row) {
				if (!empty($row['order_recurring_id'])) {
					if (is_null($log_entry_line) || ($row['log_entry_id'] !== $tmp[$log_entry_line]['log_entry_id'])) {
						$log_entry_line = $report_line++;

						$tmp[$log_entry_line] = array (
							'log_entry_id'       => $row['log_entry_id'],
							'ref_log_entry_id'   => '',
							'order_id'           => $row['order_id'],
							'date'               => $row['start_time'],
							'amount'             => 0,
							'currency_code'      => $row['currency_code'],
							'order_recurring_id' => 0,
							'status'             => $this->getLogEntryStatus($row['run_time'], $row['pid']),
						);
					}

					$tmp[$report_line++] = array (
						'log_entry_id'       => $row['reference'],
						'ref_log_entry_id'   => $row['log_entry_id'],
						'order_id'           => $row['order_id'],
						'date'               => $row['transaction_date'],
						'amount'             => $row['amount'],
						'currency_code'      => $row['currency_code'],
						'order_recurring_id' => $row['order_recurring_id'],
						'status'             => $this->getRecurringTransactionType((int)$row['type']),
					);

					$tmp[$log_entry_line]['amount'] += $row['amount'];
					$tmp[$log_entry_line]['order_recurring_id']++;
				}
			}

			foreach($tmp as $row) {
				$log_entry = array (
					'log_entry_id'              => $row['log_entry_id'],
					'ref_log_entry_id'          => $row['ref_log_entry_id'],
					'order_id'                  => '',
					'order_link'                => '',
					'order_link_title'          => '',
					'date'                      => $row['date'],
					'amount'                    => $this->currency->format($row['amount'], $row['currency_code']),
					'order_recurring_id'        => '',
					'order_recurring_btn_link'  => '',
					'order_recurring_btn_title' => '',
					'status'                    => $row['status']
				);

				if (empty($row['ref_log_entry_id'])){// Log entry summary
					$log_entry['order_recurring_id'] = sprintf(
						$this->language->get('order_recurring_total'),
						$row['order_recurring_id']
					);
				} else {// Transaction entry
					$log_entry['order_id'] = $row['order_id'];
					$log_entry['order_link'] = $this->url->link(
						'sale/order/info',
						$this->getTokenParam() . '=' . $this->getToken() . '&order_id=' . $row['order_id'],
						true
					);
					$log_entry['order_link_title'] = sprintf(
						$this->language->get('order_link_title'),
						$row['order_id']
					);

					$log_entry['order_recurring_btn_link'] = $this->url->link(
						'sale/recurring/info',
						$this->getTokenParam() . '=' . $this->getToken() . '&order_recurring_id=' . $row['order_recurring_id'],
						true
					);
					$log_entry['order_recurring_btn_title'] = sprintf(
						$this->language->get('order_recurring_btn_title'),
						$row['order_recurring_id']
					);
				}
				$result[] = $log_entry;
			}
		}

		return $result;
	}

	/**
	 * Gets the last execution time of the cron
	 *
	 * @return array
	 */
	protected function getLastCronExecTime()
	{
		$result = $this->language->get('alert_cron_not_run_yet');

		$query = $this->db->query('SELECT `start_time` FROM `' . DB_PREFIX . $this->module_name . '_cronlog` ORDER BY `log_entry_id` DESC LIMIT 1');

		if ($query->num_rows == 1) {
			$data = array_pop($query->rows);
			$result = $data['start_time'];
		}

		return $result;
	}

	/**
	 * Gets the cron execution status used in the styles in the templates
	 *
	 * @return string
	 */
	protected function getCronExecStatus()
	{
		$result = 'danger';

		$time_diff = (microtime(true) - strtotime($this->getLastCronExecTime()));

		if ($time_diff<3600) {// 1 hour
			$result = 'success';
		} elseif ($time_diff<12*3600) {// 12 hours
			$result = 'warning';
		}

		return $result;
	}

	/**
	 * Gets the Log Entry Status
	 *
	 * @param string $run_time
	 * @param string $pid
	 * @return string
	 */
	protected function getLogEntryStatus($run_time, $pid)
	{
		$status = null;

		if (is_null($run_time)) {
			if (posix_getpgid($pid) == $pid) {
				$status = sprintf($this->language->get('text_log_status_running'), $pid);
			} else {
				$status = $this->language->get('text_log_status_terminated');
			}
		} else {
			$status = sprintf($this->language->get('text_log_status_completed'), $run_time);
		}

		return $status;
	}

	/**
	 * Gets recurring transaction type
	 *
	 * @param int $type_id
	 * @return array
	 */
	protected function getRecurringTransactionType($type_id)
	{
		$result = '';

		$this->load->language('sale/recurring');

		$transaction_types = array(
			0 => 'text_transaction_date_added',
			1 => 'text_transaction_payment',
			2 => 'text_transaction_outstanding_payment',
			3 => 'text_transaction_skipped',
			4 => 'text_transaction_failed',
			5 => 'text_transaction_cancelled',
			6 => 'text_transaction_suspended',
			7 => 'text_transaction_suspended_failed',
			8 => 'text_transaction_outstanding_failed',
			9 => 'text_transaction_expired'
		);

		if (array_key_exists($type_id, $transaction_types)) {
			$result = $this->language->get($transaction_types[$type_id]);
		}

		return $result;
	}

	/**
	 * Gets a modal form link
	 *
	 * @param string $token
	 * @return string
	 */
	protected function getModalFormLink($token)
	{
		$link_parameters = array(
			'default' => array(
				'route'  => "payment/{$this->module_name}/getModalForm",
				'args'   => 'token=' . $token,
				'secure' => 'SSL'
			),
			'2.3'     => array(
				'route'  => "{$this->route_prefix}payment/{$this->module_name}",
				'args'   => 'action=getModalForm&token=' . $token,
				'secure' => 'SSL'
			),
			'3.0'     => array(
				'route'  => "{$this->route_prefix}payment/{$this->module_name}",
				'args'   => 'action=getModalForm&user_token=' . $token,
				'secure' => 'SSL'
			),
		);

		$link = $link_parameters['default'];

		if ($this->isVersion30OrAbove()) {
			$link = $link_parameters['3.0'];
		} else if ($this->isVersion23OrAbove()) {
			$link = $link_parameters['2.3'];
		}

		return $this->getLink($link);
	}

	/**
	 * Gets a payment link
	 *
	 * @param string $token
	 * @return string
	 */
	protected function getPaymentLink($token)
	{
		$link_parameters = array(
			'default' => array(
				'route'  => 'extension/payment',
				'args'   => 'token=' . $token,
				'secure' => 'SSL'
			),
			'2.3'     => array(
				'route'  => 'extension/extension',
				'args'   => 'type=payment&token=' . $token,
				'secure' => 'SSL'
			),
			'3.0'     => array(
				'route'  => 'marketplace/extension',
				'args'   => 'type=payment&user_token=' . $token,
				'secure' => 'SSL'
			),
		);

		$link = $link_parameters['default'];

		if ($this->isVersion30OrAbove()) {
			$link = $link_parameters['3.0'];
		} else if ($this->isVersion23OrAbove()) {
			$link = $link_parameters['2.3'];
		}

		return $this->getLink($link);
	}

	/**
	 * Creates a link based on the link parameters and OpenCart version
	 *
	 * @param array $link_parameters
	 * @return string
	 */
	protected function getLink($link_parameters)
	{
		return $this->url->link(
			$link_parameters['route'],
			$link_parameters['args'],
			$link_parameters['secure']);
	}

	/**
	 * @return string
	 */
	protected function getServerAddress()
	{
		$server_name = $this->request->server['SERVER_NAME'];

		if (empty($server_name) || !function_exists('gethostbyname')) {
			return $this->request->server['SERVER_ADDR'];
		}

		return gethostbyname($server_name);
	}

	/**
	 * Determine if Google Pay, PayPal ot Apple Pay Method is chosen inside the Payment settings
	 *
	 * @param string $method GooglePay or PayPal Method
	 * @return bool
	 */
	protected function isTransactionWithCustomAttribute($transaction_type)
	{
		$transaction_types = [
			\Genesis\Api\Constants\Transaction\Types::GOOGLE_PAY,
			\Genesis\Api\Constants\Transaction\Types::PAY_PAL,
			\Genesis\Api\Constants\Transaction\Types::APPLE_PAY,
		];

		return in_array($transaction_type, $transaction_types);
	}

	/**
	 * Check if canCapture
	 *
	 * @param $action
	 * @param $transaction_type
	 * @return bool
	 */
	protected function checkReferenceActionByCustomAttr($action, $transaction_type)
	{
		$selected_types = $this->config->get("{$this->module_name}_transaction_type");

		if (!is_array($selected_types)) {
			return false;
		}

		switch ($transaction_type) {
			case \Genesis\Api\Constants\Transaction\Types::GOOGLE_PAY:
				if (EMerchantPayHelper::REFERENCE_ACTION_CAPTURE === $action) {
					return in_array(
						EMerchantPayHelper::GOOGLE_PAY_TRANSACTION_PREFIX .
						EMerchantPayHelper::GOOGLE_PAY_PAYMENT_TYPE_AUTHORIZE,
						$selected_types
					);
				}

				if (EMerchantPayHelper::REFERENCE_ACTION_REFUND === $action) {
					return in_array(
						EMerchantPayHelper::GOOGLE_PAY_TRANSACTION_PREFIX .
						EMerchantPayHelper::GOOGLE_PAY_PAYMENT_TYPE_SALE,
						$selected_types
					);
				}
				break;
			case \Genesis\Api\Constants\Transaction\Types::PAY_PAL:
				if (EMerchantPayHelper::REFERENCE_ACTION_CAPTURE === $action) {
					return in_array(
						EMerchantPayHelper::PAYPAL_TRANSACTION_PREFIX .
						EMerchantPayHelper::PAYPAL_PAYMENT_TYPE_AUTHORIZE,
						$selected_types
					);
				}

				if (EMerchantPayHelper::REFERENCE_ACTION_REFUND === $action) {
					$refundable_types = [
						EMerchantPayHelper::PAYPAL_TRANSACTION_PREFIX .
						EMerchantPayHelper::PAYPAL_PAYMENT_TYPE_SALE,
						EMerchantPayHelper::PAYPAL_TRANSACTION_PREFIX .
						EMerchantPayHelper::PAYPAL_PAYMENT_TYPE_EXPRESS
					];

					return (count(array_intersect($refundable_types, $selected_types)) > 0);
				}
				break;
			case \Genesis\Api\Constants\Transaction\Types::APPLE_PAY:
				if (EMerchantPayHelper::REFERENCE_ACTION_CAPTURE === $action) {
					return in_array(
						EMerchantPayHelper::APPLE_PAY_TRANSACTION_PREFIX .
						EMerchantPayHelper::APPLE_PAY_PAYMENT_TYPE_AUTHORIZE,
						$selected_types
					);
				}

				if (EMerchantPayHelper::REFERENCE_ACTION_REFUND === $action) {
					return in_array(
						EMerchantPayHelper::APPLE_PAY_TRANSACTION_PREFIX .
						EMerchantPayHelper::APPLE_PAY_PAYMENT_TYPE_SALE,
						$selected_types
					);
				}
				break;
			default:
				return false;
		} // end Switch

		return false;
	}

	/**
	 * Check if the Genesis Transaction state is APPROVED
	 *
	 * @param $transaction_type
	 * @return bool
	 */
	protected function hasApprovedState($transaction_type)
	{
		if (empty($transaction_type)) {
			return false;
		}

		$state = new States($transaction_type);

		return $state->isApproved();
	}
}
