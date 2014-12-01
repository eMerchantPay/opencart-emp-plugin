<?php
class ControllerPaymentEmerchantPayDirect extends Controller {
	private $error = array();

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
			'heading_title'                         => $this->language->get('heading_title'),
			'text_edit'                             => $this->language->get('text_edit'),
			'text_enabled'                          => $this->language->get('text_enabled'),
			'text_disabled'                         => $this->language->get('text_disabled'),
			'text_all_zones'                        => $this->language->get('text_all_zones'),
			'text_yes'                              => $this->language->get('text_yes'),
			'text_no'                               => $this->language->get('text_no'),

			'entry_username'                        => $this->language->get('entry_username'),
			'entry_password'                        => $this->language->get('entry_password'),
			'entry_token'                           => $this->language->get('entry_token'),
			'entry_sandbox'                         => $this->language->get('entry_sandbox'),
			'entry_transaction_type'                => $this->language->get('entry_transaction_type'),

			'entry_order_status'                    => $this->language->get('entry_order_status'),
			'entry_order_status_async'              => $this->language->get('entry_order_status_async'),
			'entry_total'                           => $this->language->get('entry_total'),
			'entry_geo_zone'                        => $this->language->get('entry_geo_zone'),
			'entry_status'                          => $this->language->get('entry_status'),
			'entry_sort_order'                      => $this->language->get('entry_sort_order'),

			'help_sandbox'                          => $this->language->get('help_sandbox'),
			'help_total'                            => $this->language->get('help_total'),
			'help_order_status'                     => $this->language->get('help_order_status'),
			'help_order_status_async'               => $this->language->get('help_order_status_async'),

			'button_save'                           => $this->language->get('button_save'),
			'button_cancel'                         => $this->language->get('button_cancel'),

			'geo_zones'                             => $this->model_localisation_geo_zone->getGeoZones(),
			'order_statuses'                        => $this->model_localisation_order_status->getOrderStatuses(),
			'transaction_types'                     => $this->model_payment_emerchantpay_direct->getTransactionTypes(),

			'error_username'                        => '', //$this->language->get('error_username'),
			'error_password'                        => '', //$this->language->get('error_password'),
			'error_token'                           => '', //$this->language->get('error_token'),
			'error_warning'                         => (isset($this->error['warning'])) ? $this->error['warning'] : '',

			// Settings
			'emerchantpay_direct_username'              => $this->getFieldValue('emerchantpay_direct_username'),
			'emerchantpay_direct_password'              => $this->getFieldValue('emerchantpay_direct_password'),
			'emerchantpay_direct_token'                 => $this->getFieldValue('emerchantpay_direct_token'),
			'emerchantpay_direct_sandbox'               => $this->getFieldValue('emerchantpay_direct_sandbox'),
			'emerchantpay_direct_transaction_type'      => $this->getFieldValue('emerchantpay_direct_transaction_type'),
			'emerchantpay_direct_total'                 => $this->getFieldValue('emerchantpay_direct_total'),
			'emerchantpay_direct_order_status_id'       => $this->getFieldValue('emerchantpay_direct_order_status_id'),
			'emerchantpay_direct_async_order_status_id' => $this->getFieldValue('emerchantpay_direct_async_order_status_id'),
			'emerchantpay_direct_geo_zone_id'           => $this->getFieldValue('emerchantpay_direct_geo_zone_id'),
			'emerchantpay_direct_status'                => $this->getFieldValue('emerchantpay_direct_status'),
			'emerchantpay_direct_sort_order'            => $this->getFieldValue('emerchantpay_direct_sort_order'),

			'action'                                => $this->url->link('payment/emerchantpay_direct', 'token=' . $this->session->data['token'], 'SSL'),
			'cancel'                                => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),

			'header'                                => $this->load->controller('common/header'),
			'column_left'                           => $this->load->controller('common/column_left'),
			'footer'                                => $this->load->controller('common/footer')
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

			$this->load->model('payment/emerchantpay_direct');

			$transactions = $this->model_payment_emerchantpay_direct->getTransactionsByOrder($this->request->get['order_id']);

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

			echo $this->load->view('payment/emerchantpay_direct_order_modal.tpl', $data);
		}
	}

	public function capture() {
		$this->load->language('payment/emerchantpay_direct');
		$json = array();

		if (isset($this->request->post['reference_id']) && trim($this->request->post['reference_id']) != '') {
			$this->load->model('payment/emerchantpay_direct');

			$transaction = $this->model_payment_emerchantpay_direct->getTransactionById($this->request->post['reference_id']);

			if (is_array($transaction)) {
				$amount = $this->request->post['amount'];

				$capture = $this->model_payment_emerchantpay_direct->capture($transaction['unique_id'], $amount, $transaction['currency']);

				if (is_object($capture)) {
					$data = array(
						'order_id'          => $transaction['order_id'],
						'unique_id'         => $capture->unique_id,
						'type'              => $capture->type,
						'mode'              => $capture->mode,
						'timestamp'         => $capture->timestamp,
						'status'            => $capture->status,
						'amount'            => $capture->amount,
						'currency'          => $capture->currency,
						'message'           => isset($capture->message) ? $capture->message : '',
						'technical_message' => isset($capture->technical_message) ? $capture->technical_message : '',
					);

					$this->model_payment_emerchantpay_direct->addTransaction($data);

					$json = array(
						'error' => false,
						'text'  => $this->language->get('text_response_capture')
					);
				}
			}
		}
		else {
			$json = array(
				'error' => true,
				'text'  => 'Invalid request, please try again!'
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function refund() {
		$this->load->language('payment/emerchantpay_direct');
		$json = array();

		if (isset($this->request->post['reference_id']) && trim($this->request->post['reference_id']) != '') {
			$this->load->model('payment/emerchantpay_direct');

			$transaction = $this->model_payment_emerchantpay_direct->getTransactionById($this->request->post['reference_id']);

			if (is_array($transaction)) {
				$amount = $this->request->post['amount'];

				$refund = $this->model_payment_emerchantpay_direct->refund($transaction['unique_id'], $amount, $transaction['currency']);

				if (is_object($refund)) {
					$data = array(
						'order_id'          => $transaction['order_id'],
						'unique_id'         => $refund->unique_id,
						'type'              => $refund->type,
						'mode'              => $refund->mode,
						'timestamp'         => $refund->timestamp,
						'status'            => $refund->status,
						'amount'            => $refund->amount,
						'currency'          => $refund->currency,
						'message'           => isset($refund->message) ? $refund->message : '',
						'technical_message' => isset($refund->technical_message) ? $refund->technical_message : '',
					);

					$this->model_payment_emerchantpay_direct->addTransaction($data);

					$json = array(
						'error' => false,
						'text'  => $this->language->get('text_response_refund')
					);
				}
			}
		}
		else {
			$json = array(
				'error' => true,
				'text'  => 'Invalid request, please try again!'
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function void() {
		$this->load->language('payment/emerchantpay_direct');
		$json = array();

		if (isset($this->request->post['reference_id']) && trim($this->request->post['reference_id']) != '') {
			$this->load->model('payment/emerchantpay_direct');

			$transaction = $this->model_payment_emerchantpay_direct->getTransactionById($this->request->post['reference_id']);

			if (is_array($transaction)) {
				$void = $this->model_payment_emerchantpay_direct->void($transaction['unique_id'], $this->request->post['message']);

				if (is_object($void)) {
					$data = array(
						'order_id'          => $transaction['order_id'],
						'unique_id'         => $void->unique_id,
						'type'              => $void->type,
						'mode'              => $void->mode,
						'timestamp'         => $void->timestamp,
						'status'            => $void->status,
						'amount'            => $void->amount,
						'currency'          => $void->currency,
						'message'           => isset($void->message) ? $void->message : '',
						'technical_message' => isset($void->technical_message) ? $void->technical_message : '',
					);

					$this->model_payment_emerchantpay_direct->addTransaction($data);

					$json = array(
						'error' => false,
						'text'  => $this->language->get('text_response_void')
					);
				}
			}
		}
		else {
			$json = array(
				'error' => true,
				'text'  => 'Invalid request, please try again!'
			);
		}

		/*
		$this->model_payment_sagepay_direct->logger('Void result:\r\n' . print_r($void_response, 1));

		if (is_object($void_response)) {
			$this->model_payment_sagepay_direct->addTransaction($sagepay_direct_order['sagepay_direct_order_id'], 'void', 0.00);
			$this->model_payment_sagepay_direct->updateVoidStatus($sagepay_direct_order['sagepay_direct_order_id'], 1);

			$json['msg'] = $this->language->get('text_void_ok');

			$json['data'] = array();
			$json['data']['date_added'] = date("Y-m-d H:i:s");
			$json['error'] = false;
		} else {
			$json['error'] = true;
			$json['msg'] = isset($void_response['StatuesDetail']) && !empty($void_response['StatuesDetail']) ? (string)$void_response['StatuesDetail'] : 'Unable to void';
		}
		*/

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/emerchantpay_direct')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function getFieldValue($key) {
		if (isset($this->request->post[$key])) {
			return $this->request->post[$key];
		}

		return $this->config->get($key);
	}

	protected function bootstrapGenesis() {
		$this->load->model('payment/emerchantpay_direct');
		$this->model_payment_emerchantpay_direct->bootstrapGenesis();
	}

	public function install() {
		$this->load->model('payment/emerchantpay_direct');
		$this->model_payment_emerchantpay_direct->install();
	}

	public function uninstall() {
		$this->load->model('payment/emerchantpay_direct');
		$this->model_payment_emerchantpay_direct->uninstall();
	}
}