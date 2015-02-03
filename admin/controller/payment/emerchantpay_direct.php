<?php
class ControllerPaymentEmerchantPayDirect extends Controller {

	public function index() {
		$this->load->language('payment/emerchantpay_direct');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('emerchantpay_direct', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->load->model('localisation/geo_zone');
		$this->load->model('localisation/order_status');
		$this->load->model('payment/emerchantpay_direct');

		$data = array(
			'heading_title'                             => $this->language->get('heading_title'),
			'text_edit'                                 => $this->language->get('text_edit'),
			'text_enabled'                              => $this->language->get('text_enabled'),
			'text_disabled'                             => $this->language->get('text_disabled'),
			'text_all_zones'                            => $this->language->get('text_all_zones'),
			'text_yes'                                  => $this->language->get('text_yes'),
			'text_no'                                   => $this->language->get('text_no'),

			'entry_username'                            => $this->language->get('entry_username'),
			'entry_password'                            => $this->language->get('entry_password'),
			'entry_token'                               => $this->language->get('entry_token'),
			'entry_sandbox'                             => $this->language->get('entry_sandbox'),
			'entry_transaction_type'                    => $this->language->get('entry_transaction_type'),

			'entry_order_status'                        => $this->language->get('entry_order_status'),
			'entry_async_order_status'                  => $this->language->get('entry_async_order_status'),
			'entry_failure_order_status'                => $this->language->get('entry_failure_order_status'),
			'entry_total'                               => $this->language->get('entry_total'),
			'entry_geo_zone'                            => $this->language->get('entry_geo_zone'),
			'entry_status'                              => $this->language->get('entry_status'),
			'entry_debug'                               => $this->language->get('entry_debug'),
			'entry_sort_order'                          => $this->language->get('entry_sort_order'),

			'help_sandbox'                              => $this->language->get('help_sandbox'),
			'help_total'                                => $this->language->get('help_total'),
			'help_order_status'                         => $this->language->get('help_order_status'),
			'help_async_order_status'                   => $this->language->get('help_async_order_status'),
			'help_failure_order_status'                 => $this->language->get('help_failure_order_status'),

			'button_save'                               => $this->language->get('button_save'),
			'button_cancel'                             => $this->language->get('button_cancel'),

			'geo_zones'                                 => $this->model_localisation_geo_zone->getGeoZones(),
			'order_statuses'                            => $this->model_localisation_order_status->getOrderStatuses(),
			'transaction_types'                         => $this->model_payment_emerchantpay_direct->getTransactionTypes(),

			'error_username'                            => '', //$this->language->get('error_username'),
			'error_password'                            => '', //$this->language->get('error_password'),
			'error_token'                               => '', //$this->language->get('error_token'),
			'error_warning'                             => (isset($this->error['warning'])) ? $this->error['warning'] : '',

			// Settings
			'emerchantpay_direct_username'                  => $this->getFieldValue('emerchantpay_direct_username'),
			'emerchantpay_direct_password'                  => $this->getFieldValue('emerchantpay_direct_password'),
			'emerchantpay_direct_token'                     => $this->getFieldValue('emerchantpay_direct_token'),
			'emerchantpay_direct_sandbox'                   => $this->getFieldValue('emerchantpay_direct_sandbox'),
			'emerchantpay_direct_transaction_type'          => $this->getFieldValue('emerchantpay_direct_transaction_type'),
			'emerchantpay_direct_total'                     => $this->getFieldValue('emerchantpay_direct_total'),
			'emerchantpay_direct_order_status_id'           => $this->getFieldValue('emerchantpay_direct_order_status_id'),
			'emerchantpay_direct_failure_order_status_id'   => $this->getFieldValue('emerchantpay_direct_failure_order_status_id'),
			'emerchantpay_direct_async_order_status_id'     => $this->getFieldValue('emerchantpay_direct_async_order_status_id'),
			'emerchantpay_direct_geo_zone_id'               => $this->getFieldValue('emerchantpay_direct_geo_zone_id'),
			'emerchantpay_direct_status'                    => $this->getFieldValue('emerchantpay_direct_status'),
			'emerchantpay_direct_sort_order'                => $this->getFieldValue('emerchantpay_direct_sort_order'),
			'emerchantpay_direct_debug'                     => $this->getFieldValue('emerchantpay_direct_debug'),

			'action'                                    => $this->url->link('payment/emerchantpay_direct', 'token=' . $this->session->data['token'], 'SSL'),
			'cancel'                                    => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),

			'header'                                    => $this->load->controller('common/header'),
			'column_left'                               => $this->load->controller('common/column_left'),
			'footer'                                    => $this->load->controller('common/footer')
		);

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_payment'),
			'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('payment/emerchantpay_direct', 'token=' . $this->session->data['token'], 'SSL')
		);

		$this->response->setOutput($this->load->view('payment/emerchantpay_direct.tpl', $data));
	}

	public function orderAction() {
		if ($this->config->get('emerchantpay_direct_status')) {

			$this->load->language('payment/emerchantpay_direct');

			$this->load->model('payment/emerchantpay_direct');

			$this->document->addStyle('view/javascript/treegrid/css/jquery.treegrid.css');

			$this->document->addScript('view/javascript/treegrid/js/jquery.treegrid.js');
			$this->document->addScript('view/javascript/treegrid/js/jquery.treegrid.bootstrap3.js');

			$transactions = $this->model_payment_emerchantpay_direct->getTransactionsByOrder($this->request->get['order_id']);

			if ($transactions) {
				// Process individual fields
				foreach ($transactions as &$transaction) {
					$transaction['amount']    = $this->currency->format($transaction['amount'], $transaction['currency']);
					$transaction['timestamp'] = date('H:i:s m/d/Y', strtotime($transaction['timestamp']));

					if (in_array( $transaction['type'], array( 'authorize', 'authorize3d')) && $transaction['status'] == 'approved') {
						$transaction['can_capture'] = true;
					}
					else {
						$transaction['can_capture'] = false;
					}

					if (in_array( $transaction['type'], array( 'authorize', 'authorize3d', 'capture', 'sale', 'sale3d', 'init_recurring_sale', 'recurring_sale' )) && $transaction['status'] == 'approved') {
						$transaction['can_refund'] = true;
					} else {
						$transaction['can_refund'] = false;
					}

					if (in_array( $transaction['type'], array( 'authorize', 'authorize3d', 'capture', 'sale', 'sale3d', 'init_recurring_sale', 'recurring_sale', 'refund' )) && $transaction ) {
						$transaction['can_void'] = true;
					} else {
						$transaction['can_void'] = false;
					}
				}

				// Sort the transactions list in the following order:
				//
				// 1. Sort by timestamp (date), i.e. most-recent transactions on top
				// 2. Sort by relations, i.e. every parent has the child nodes immediately after

				// Ascending Date/Timestmap sorting
				uasort($transactions, function($a, $b) {
					// sort by timestamp (date) first
					if (@$a["timestamp"] == @$b["timestamp"]){
						return 0;
					}
					return (@$a["timestamp"] > @$b["timestamp"]) ? 1 : -1;
				});

				// Create the parent/child relations from a flat array
				$array_asc = array();

				foreach($transactions as $key => $val){
					// create an array with ids as keys and children
					// with the assumption that parents are created earlier.
					// store the original key
					$array_asc[$val['unique_id']] = array_merge($val, array('org_key' => $key));
					if ($val['reference_id']){
						$array_asc[$val['reference_id']]['children'][] = $val['unique_id'];
					}
				}

				// Order the parent/child entries
				$transactions = array();

				foreach($array_asc as $val){
					if (isset($val['reference_id']) && $val['reference_id']){
						continue;
					}

					$this->sortTrxByRelation($transactions, $val, $array_asc);
				}

				$data = array(

					'text_payment_info'             => $this->language->get('text_payment_info'),
					'text_transaction_id'           => $this->language->get('text_transaction_id'),
					'text_transaction_timestamp'    => $this->language->get('text_transaction_timestamp'),
					'text_transaction_amount'       => $this->language->get('text_transaction_amount'),
					'text_transaction_status'       => $this->language->get('text_transaction_status'),
					'text_transaction_type'         => $this->language->get('text_transaction_type'),
					'text_transaction_message'      => $this->language->get('text_transaction_message'),
					'text_transaction_mode'         => $this->language->get('text_transaction_mode'),
					'text_transaction_action'       => $this->language->get('text_transaction_action'),

					'order_id'      => $this->request->get['order_id'],
					'token'         => $this->request->get['token'],

					'url_modal'     => htmlspecialchars_decode($this->url->link('payment/emerchantpay_direct/getModalForm', 'token=' . $this->session->data['token'], 'SSL')),

					'transactions'  => $transactions,
				);

				return $this->load->view('payment/emerchantpay_direct_order.tpl', $data);
			}

			/*
			if ($transactions) {
				$this->load->language('payment/emerchantpay_direct');

				foreach ($transactions as &$transaction) {
					$transaction['amount']    = $this->currency->format($transaction['amount'], $transaction['currency']);
					$transaction['timestamp'] = date('H:i:s m/d/Y', strtotime($transaction['timestamp']));

					if (in_array($transaction['type'], array('authorize', 'authorize3d'))) {
						$transaction['can_capture'] = true;
					}
					else {
						$transaction['can_capture'] = false;
					}

					if (in_array($transaction['type'], array('authorize', 'authorize3d', 'sale', 'sale3d', 'init recurring sale', 'recurring sale'))) {
						$transaction['can_refund'] = true;
					} else {
						$transaction['can_refund'] = false;
					}

					if ($transaction['type'] != 'void') {
						$transaction['can_void'] = true;
					} else {
						$transaction['can_void'] = false;
					}
				}

				$data = array(

					'text_payment_info'             => $this->language->get('text_payment_info'),
					'text_transaction_id'           => $this->language->get('text_transaction_id'),
					'text_transaction_timestamp'    => $this->language->get('text_transaction_timestamp'),
					'text_transaction_amount'       => $this->language->get('text_transaction_amount'),
					'text_transaction_status'       => $this->language->get('text_transaction_status'),
					'text_transaction_type'         => $this->language->get('text_transaction_type'),
					'text_transaction_message'      => $this->language->get('text_transaction_message'),
					'text_transaction_mode'         => $this->language->get('text_transaction_mode'),
					'text_transaction_action'       => $this->language->get('text_transaction_action'),

					'order_id'      => $this->request->get['order_id'],
					'token'         => $this->request->get['token'],

					'url_modal'     => htmlspecialchars_decode($this->url->link('payment/emerchantpay_direct/getModalForm', 'token=' . $this->session->data['token'], 'SSL')),

					'transactions'  => $transactions,
				);

				return $this->load->view('payment/emerchantpay_direct_order.tpl', $data);
			}
			*/
		}
	}

	public function getModalForm() {
		if (isset($this->request->post['reference_id']) && isset($this->request->post['type'])) {
			$this->load->language('payment/emerchantpay_direct');
			$this->load->model('payment/emerchantpay_direct');

			$reference_id   = $this->request->post['reference_id'];
			$type           = $this->request->post['type'];

			$transaction = $this->model_payment_emerchantpay_direct->getTransactionById($reference_id);

			$data = array(
				'type'          => $type,
				'transaction'   => $transaction,

				'url_action' => $this->url->link('payment/emerchantpay_direct/' . $type, 'token=' . $this->session->data['token'], 'SSL'),

				'text_button_close'             => $this->language->get('text_button_close'),
				'text_button_capture_partial'   => $this->language->get('text_button_capture_partial'),
				'text_button_capture_full'      => $this->language->get('text_button_capture_full'),
				'text_button_refund_partial'    => $this->language->get('text_button_refund_partial'),
				'text_button_refund_full'       => $this->language->get('text_button_refund_full'),
				'text_button_void'              => $this->language->get('text_button_void'),

				'text_modal_title_capture'      => $this->language->get('text_modal_title_capture'),
				'text_modal_title_refund'       => $this->language->get('text_modal_title_refund'),
				'text_modal_title_void'         => $this->language->get('text_modal_title_void')
			);

			$this->response->setOutput($this->load->view('payment/emerchantpay_direct_order_modal.tpl', $data));
		}
	}

	public function capture() {
		$this->load->language('payment/emerchantpay_direct');

		if (isset($this->request->post['reference_id']) && trim($this->request->post['reference_id']) != '') {
			$this->load->model('payment/emerchantpay_direct');

			$transaction = $this->model_payment_emerchantpay_direct->getTransactionById($this->request->post['reference_id']);

			if (is_array($transaction)) {
				$amount = $this->request->post['amount'];

				$message = isset($this->request->post['message']) ? $this->request->post['message'] : '';

				$capture = $this->model_payment_emerchantpay_direct->capture($transaction['unique_id'], $amount, $transaction['currency'], $message);

				if (isset($capture->response) && !$capture->error) {

					$amount = $this->model_payment_emerchantpay_direct->iso4217ConvertAmount($capture->response->amount, $capture->response->currency);

					$data = array(
						'order_id'          => $transaction['order_id'],
						'unique_id'         => $capture->response->unique_id,
						'reference_id'      => $transaction['unique_id'],
						'type'              => $capture->response->transaction_type,
						'mode'              => $capture->response->mode,
						'timestamp'         => $capture->response->timestamp,
						'status'            => $capture->response->status,
						'amount'            => $amount,
						'currency'          => $capture->response->currency,
						'message'           => isset($capture->response->message) ? $capture->response->message : '',
						'technical_message' => isset($capture->response->technical_message) ? $capture->response->technical_message : '',
					);

					$this->model_payment_emerchantpay_direct->addTransaction($data);

					$json = array(
						'error' => false,
						'text'  => isset($capture->message) ? $capture->message : $this->language->get('text_response_success')
					);
				}
				else {
					$json = array(
						'error' => true,
						'text'  => isset($capture->message) ? $capture->message : $this->language->get('text_response_failure')
					);
				}
			}
			else {
				$json = array(
					'error' => true,
					'text'  => $this->language->get('text_invalid_reference_id'),
				);
			}
		}
		else {
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

	public function refund() {
		$this->load->language('payment/emerchantpay_direct');

		if (isset($this->request->post['reference_id']) && trim($this->request->post['reference_id']) != '') {
			$this->load->model('payment/emerchantpay_direct');

			$transaction = $this->model_payment_emerchantpay_direct->getTransactionById($this->request->post['reference_id']);

			if (isset($transaction['order_id']) && intval($transaction['order_id']) > 0) {
				$amount = $this->request->post['amount'];

				$message = isset($this->request->post['message']) ? $this->request->post['message'] : '';

				$refund = $this->model_payment_emerchantpay_direct->refund($transaction['unique_id'], $amount, $transaction['currency'], $message);

				if (isset($refund->response) && !$refund->error) {
					$amount = $this->model_payment_emerchantpay_direct->iso4217ConvertAmount($refund->response->amount, $refund->response->currency);

					$data = array(
						'order_id'          => $transaction['order_id'],
						'unique_id'         => $refund->response->unique_id,
						'reference_id'      => $transaction['unique_id'],
						'type'              => $refund->response->transaction_type,
						'mode'              => $refund->response->mode,
						'timestamp'         => $refund->response->timestamp,
						'status'            => $refund->response->status,
						'amount'            => $amount,
						'currency'          => $refund->response->currency,
						'message'           => isset($refund->response->message) ? $refund->response->message : '',
						'technical_message' => isset($refund->response->technical_message) ? $refund->response->technical_message : '',
					);

					$this->model_payment_emerchantpay_direct->addTransaction($data);

					$json = array(
						'error' => false,
						'text'  => isset($refund->message) ? $refund->message : $this->language->get('text_response_success')
					);
				}
				else {
					$json = array(
						'error' => true,
						'text'  => isset($refund->message) ? $refund->message : $this->language->get('text_response_failure')
					);
				}
			}
			else {
				$json = array(
					'error' => true,
					'text'  => $this->language->get('text_invalid_reference_id'),
				);
			}
		}
		else {
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

	public function void() {
		$this->load->language('payment/emerchantpay_direct');

		if (isset($this->request->post['reference_id']) && trim($this->request->post['reference_id']) != '') {
			$this->load->model('payment/emerchantpay_direct');

			$transaction = $this->model_payment_emerchantpay_direct->getTransactionById($this->request->post['reference_id']);

			if (isset($transaction['order_id']) && intval($transaction['order_id']) > 0) {
				$message = isset($this->request->post['message']) ? $this->request->post['message'] : '';

				$void = $this->model_payment_emerchantpay_direct->void($transaction['unique_id'], $message);

				if (isset($void->response) && !$void->error) {
					$amount = $this->model_payment_emerchantpay_direct->iso4217ConvertAmount($void->response->amount, $void->response->currency);

					$data = array(
						'order_id'          => $transaction['order_id'],
						'unique_id'         => $void->response->unique_id,
						'reference_id'      => $transaction['unique_id'],
						'type'              => $void->response->transaction_type,
						'mode'              => $void->response->mode,
						'timestamp'         => $void->response->timestamp,
						'status'            => $void->response->status,
						'amount'            => $amount,
						'currency'          => $void->response->currency,
						'message'           => isset($void->response->message) ? $void->response->message : '',
						'technical_message' => isset($void->response->technical_message) ? $void->response->technical_message : '',
					);

					$this->model_payment_emerchantpay_direct->addTransaction($data);

					$json = array(
						'error' => false,
						'text'  => isset($void->message) ? $void->message : $this->language->get('text_response_success')
					);
				}
				else {
					$json = array(
						'error' => true,
						'text'  => isset($void->message) ? $void->message : $this->language->get('text_response_failure')
					);
				}
			}
			else {
				$json = array(
					'error' => true,
					'text'  => $this->language->get('text_invalid_reference_id'),
				);
			}
		}
		else {
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
	 * Ensure that the current user has permissions to see/modify this module
	 *
	 * @return bool
	 */
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/emerchantpay_direct')) {
			$this->error['warning'] = $this->language->get('error_permission');
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
	protected function getFieldValue($key) {
		if (isset($this->request->post[$key])) {
			return $this->request->post[$key];
		}

		return $this->config->get($key);
	}

	/**
	 * Add/Install Module Handling
	 *
	 * @return void
	 */
	public function install() {
		$this->load->model('payment/emerchantpay_direct');
		$this->model_payment_emerchantpay_direct->install();
	}

	/**
	 * Remove/Uninstall Module Handling
	 *
	 * @return void
	 */
	public function uninstall() {
		$this->load->model('payment/emerchantpay_direct');
		$this->model_payment_emerchantpay_direct->uninstall();
	}

	/**
	 * Recursive function used in the process of sorting
	 * the Transactions list
	 *
	 * @param $array_out array
	 * @param $val array
	 * @param $array_asc array
	 */
	private function sortTrxByRelation(&$array_out, $val, $array_asc){
		if (isset($val['org_key'])) {
			$array_out[ $val['org_key'] ] = $val;

			if ( isset( $val['children'] ) && sizeof( $val['children'] ) ) {
				foreach ( $val['children'] as $id ) {
					$this->sortTrxByRelation( $array_out, $array_asc[ $id ], $array_asc );
				}
			}
			unset( $array_out[ $val['org_key'] ]['children'], $array_out[ $val['org_key'] ]['org_key'] );
		}
	}
}