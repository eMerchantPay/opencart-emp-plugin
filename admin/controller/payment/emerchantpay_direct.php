<?php
/*
 * Copyright (C) 2016 eMerchantPay Ltd.
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
 * @author      eMerchantPay
 * @copyright   2016 eMerchantPay Ltd.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2 (GPL-2.0)
 */

/**
 * Backend controller for the "eMerchantPay Direct" module
 *
 * @package EMerchantPayDirect
 */
class ControllerPaymentEmerchantPayDirect extends Controller
{
    /**
     * Error storage
     *
     * @var array
     */
    private $error = array();

    /**
     * Module Name (Used in View - Templates)
     *
     * @var string
     */
    private $module_name = 'emerchantpay_direct';

    /**
     * Entry-point
     *
     * @return void
     */
    public function index()
    {
        $this->load->language('payment/emerchantpay_direct');
        $this->load->model('setting/setting');

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            try {
                if ($this->validate()) {
                    $this->model_setting_setting->editSetting($this->module_name, $this->request->post);

                    $json = array(
                        'success' => 1,
                        'text'    => $this->language->get('text_success'),
                    );
                } else {
                    $errorMessage = "";
                    $errorFieldKeyList = array(
                        'warning',
                        'username',
                        'password',
                        'token',
                        'transaction_type'
                    );

                    foreach ($errorFieldKeyList as $errorFieldKey)
                        if (isset($this->error[$errorFieldKey]))
                            $errorMessage .= sprintf("<li>%s</li>", $this->error[$errorFieldKey]);

                    $errorMessage = sprintf("<ul>%s</ul>", $errorMessage);

                    $json = array(
                        'success' => 0,
                        'text'    => $errorMessage
                    );
                }

                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
            }
            catch (\Exception $e) {
                $this->response->addHeader('HTTP/1.0 500 Internal Server Error');
            }
        }
        else {
            $this->addExternalResources(array(
                'bootstrapValidator',
                'bootstrapCheckbox',
                'commonStyleSheet'
            ));

            if (!$this->isSecureConnection()) {
                $this->error['warning'] = $this->language->get('error_https');
            }

            $headingTitle = $this->language->get('heading_title');
            $this->document->setTitle($headingTitle);
            $this->load->model('localisation/geo_zone');
            $this->load->model('localisation/order_status');
            $this->load->model('payment/emerchantpay_direct');

            $data = array(
                'heading_title'  => $headingTitle,
                'text_edit'      => $this->language->get('text_edit'),
                'text_enabled'   => $this->language->get('text_enabled'),
                'text_disabled'  => $this->language->get('text_disabled'),
                'text_all_zones' => $this->language->get('text_all_zones'),
                'text_yes'       => $this->language->get('text_yes'),
                'text_no'        => $this->language->get('text_no'),
                'text_success'   => $this->language->get('text_success'),
                'text_failed'    => $this->language->get('text_failed'),

                'entry_username'         => $this->language->get('entry_username'),
                'entry_password'         => $this->language->get('entry_password'),
                'entry_token'            => $this->language->get('entry_token'),
                'entry_sandbox'          => $this->language->get('entry_sandbox'),
                'entry_transaction_type' => $this->language->get('entry_transaction_type'),

                'entry_order_status'             => $this->language->get('entry_order_status'),
                'entry_async_order_status'       => $this->language->get('entry_async_order_status'),
                'entry_failure_order_status'     => $this->language->get('entry_failure_order_status'),
                'entry_total'                    => $this->language->get('entry_total'),
                'entry_geo_zone'                 => $this->language->get('entry_geo_zone'),
                'entry_status'                   => $this->language->get('entry_status'),
                'entry_debug'                    => $this->language->get('entry_debug'),
                'entry_sort_order'               => $this->language->get('entry_sort_order'),
                'entry_supports_partial_capture' => $this->language->get('entry_supports_partial_capture'),
                'entry_supports_partial_refund'  => $this->language->get('entry_supports_partial_refund'),
                'entry_supports_void'            => $this->language->get('entry_supports_void'),

                'help_sandbox'                  => $this->language->get('help_sandbox'),
                'help_total'                    => $this->language->get('help_total'),
                'help_order_status'             => $this->language->get('help_order_status'),
                'help_async_order_status'       => $this->language->get('help_async_order_status'),
                'help_failure_order_status'     => $this->language->get('help_failure_order_status'),
                'help_supports_partial_capture' => $this->language->get("help_supports_partial_capture"),
                'help_supports_partial_refund'  => $this->language->get("help_supports_partial_refund"),
                'help_supports_void'            => $this->language->get("help_supports_void"),

                'button_save'   => $this->language->get('button_save'),
                'button_cancel' => $this->language->get('button_cancel'),

                'geo_zones'         => $this->model_localisation_geo_zone->getGeoZones(),
                'order_statuses'    => $this->model_localisation_order_status->getOrderStatuses(),
                'transaction_types' => $this->model_payment_emerchantpay_direct->getTransactionTypes(),

                'error_username'             => $this->language->get("error_username"),
                'error_password'             => $this->language->get("error_password"),
                'error_token'                => $this->language->get("error_token"),
                'error_transaction_type'     => $this->language->get("error_transaction_type"),
                'error_warning'              => isset($this->error['warning']) ? $this->error['warning'] : '',
                'error_controls_invalidated' => $this->language->get('error_controls_invalidated'),

                // Settings
                'emerchantpay_direct_username'                 => $this->getFieldValue('emerchantpay_direct_username'),
                'emerchantpay_direct_password'                 => $this->getFieldValue('emerchantpay_direct_password'),
                'emerchantpay_direct_token'                    => $this->getFieldValue('emerchantpay_direct_token'),
                'emerchantpay_direct_sandbox'                  => $this->getFieldValue('emerchantpay_direct_sandbox'),
                'emerchantpay_direct_transaction_type'         => $this->getFieldValue('emerchantpay_direct_transaction_type'),
                'emerchantpay_direct_total'                    => $this->getFieldValue('emerchantpay_direct_total'),
                'emerchantpay_direct_order_status_id'          => $this->getFieldValue('emerchantpay_direct_order_status_id'),
                'emerchantpay_direct_failure_order_status_id'  => $this->getFieldValue('emerchantpay_direct_failure_order_status_id'),
                'emerchantpay_direct_async_order_status_id'    => $this->getFieldValue('emerchantpay_direct_async_order_status_id'),
                'emerchantpay_direct_geo_zone_id'              => $this->getFieldValue('emerchantpay_direct_geo_zone_id'),
                'emerchantpay_direct_status'                   => $this->getFieldValue('emerchantpay_direct_status'),
                'emerchantpay_direct_sort_order'               => $this->getFieldValue('emerchantpay_direct_sort_order'),
                'emerchantpay_direct_debug'                    => $this->getFieldValue('emerchantpay_direct_debug'),
                'emerchantpay_direct_supports_partial_capture' => $this->getFieldValue('emerchantpay_direct_supports_partial_capture'),
                'emerchantpay_direct_supports_partial_refund'  => $this->getFieldValue('emerchantpay_direct_supports_partial_refund'),
                'emerchantpay_direct_supports_void'            => $this->getFieldValue('emerchantpay_direct_supports_void'),

                'action' => $this->url->link('payment/emerchantpay_direct', 'token=' . $this->session->data['token'], 'SSL'),
                'cancel' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),

                'header'      => $this->load->controller('common/header'),
                'column_left' => $this->load->controller('common/column_left'),
                'footer'      => $this->load->controller('common/footer'),

                'module_name' => $this->module_name
            );

            $defaultParamValues = array(
                'emerchantpay_direct_sandbox'                  => 1,
                'emerchantpay_direct_status'                   => 0,
                'emerchantpay_direct_debug'                    => 1,
                'emerchantpay_direct_supports_partial_capture' => 1,
                'emerchantpay_direct_supports_partial_refund'  => 1,
                'emerchantpay_direct_supports_void'            => 1
            );

            foreach ($defaultParamValues as $key => $defaultValue)
                $data[$key] = (!is_numeric($data[$key]) ? $defaultValue : $data[$key]);

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
                'text' => $headingTitle,
                'href' => $this->url->link('payment/emerchantpay_direct', 'token=' . $this->session->data['token'], 'SSL')
            );

            $this->response->setOutput(
                $this->load->view('payment/emerchantpay_direct.tpl', $data)
            );
        }
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
     */
    public function orderAction()
    {
        if ($this->config->get('emerchantpay_direct_status')) {

            $this->load->language('payment/emerchantpay_direct');

            $this->load->model('payment/emerchantpay_direct');

            $this->addExternalResources(array(
                'treeGrid',
                'bootstrapValidator',
                'jQueryNumber',
                'commonStyleSheet'
            ));

            $orderId = $this->request->get['order_id'];
            $transactions = $this->model_payment_emerchantpay_direct->getTransactionsByOrder($this->request->get['order_id']);

            $hasCurrencyMethodHas = method_exists($this->currency, 'has');

            if ($transactions) {
                // Process individual fields
                foreach ($transactions as &$transaction) {
                    /* OpenCart 2.2.x Fix (Cart\Currency does not check if the given currency code exists */
                    if (($hasCurrencyMethodHas && $this->currency->has($transaction['currency'])) || (!$hasCurrencyMethodHas && !@empty($transaction['currency'])))
                        $transaction['amount'] = $this->currency->format($transaction['amount'], $transaction['currency']);
                    else /* No Currency Code is stored on Void Transaction */
                        $transaction['amount'] = "";

                    $transaction['timestamp'] = date('H:i:s m/d/Y', strtotime($transaction['timestamp']));

                    if (in_array($transaction['type'], array('authorize', 'authorize3d')) && $transaction['status'] == 'approved') {
                        $transaction['can_capture'] = true;
                    } else {
                        $transaction['can_capture'] = false;
                    }

                    if (in_array($transaction['type'], array('capture', 'sale', 'sale3d', 'init_recurring_sale', 'recurring_sale')) && $transaction['status'] == 'approved') {
                        $transaction['can_refund'] = true;
                    } else {
                        $transaction['can_refund'] = false;
                    }

                    if (in_array($transaction['type'], array('authorize', 'authorize3d', 'capture', 'sale', 'sale3d', 'init_recurring_sale', 'recurring_sale', 'refund')) && $transaction) {
                        $transaction['can_void']    = true;
                        $transaction['void_exists'] = $this->model_payment_emerchantpay_direct->getTransactionsByTypeAndStatus($orderId, $transaction['unique_id'], 'void', 'approved') !== false;
                    } else {
                        $transaction['can_void']    = false;
                    }
                }

                // Sort the transactions list in the following order:
                //
                // 1. Sort by timestamp (date), i.e. most-recent transactions on top
                // 2. Sort by relations, i.e. every parent has the child nodes immediately after

                // Ascending Date/Timestamp sorting
                uasort($transactions, function ($a, $b) {
                    // sort by timestamp (date) first
                    if (@$a["timestamp"] == @$b["timestamp"]) {
                        return 0;
                    }
                    return (@$a["timestamp"] > @$b["timestamp"]) ? 1 : -1;
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

                    'emerchantpay_direct_supports_void'              => $this->config->get('emerchantpay_direct_supports_void'),

                    'order_id'                   => $orderId,
                    'token'                      => $this->request->get['token'],
                    'url_modal'                  => htmlspecialchars_decode(
                        $this->url->link(
                            'payment/emerchantpay_direct/getModalForm', 'token=' . $this->session->data['token'], 'SSL'
                        )
                    ),
                    'module_name'                => $this->module_name,
                    'currency'		             => $this->getTemplateCurrencyArray(),
                    'transactions'               => $transactions,
                );

                return $this->load->view('payment/emerchantpay_direct_order.tpl', $data);
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
            $this->load->language('payment/emerchantpay_direct');
            $this->load->model('payment/emerchantpay_direct');

            $reference_id = $this->request->post['reference_id'];
            $type         = $this->request->post['type'];
            $orderId      = $this->request->post['order_id'];

            $transaction = $this->model_payment_emerchantpay_direct->getTransactionById($reference_id);

            if ($type == 'capture') {
                $totalAuthorizedAmount           = $this->model_payment_emerchantpay_direct->getTransactionsSumAmount($orderId, $transaction['reference_id'], array('authorize', 'authorize3d'), 'approved');
                $totalCapturedAmount             = $this->model_payment_emerchantpay_direct->getTransactionsSumAmount($orderId, $transaction['unique_id'], 'capture', 'approved');
                $transaction['available_amount'] = $totalAuthorizedAmount - $totalCapturedAmount;
            }
            else if ($type == 'refund') {
                $hasVoidTransaction = $this->model_payment_emerchantpay_direct->getTransactionsByTypeAndStatus($orderId, $transaction['unique_id'], 'void', 'approved');
                if (!$hasVoidTransaction) {
                    $totalCapturedAmount             = $transaction['amount'];
                    $totalRefundedAmount             = $this->model_payment_emerchantpay_direct->getTransactionsSumAmount($orderId, $transaction['unique_id'], 'refund', 'approved');
                    $transaction['available_amount'] = $totalCapturedAmount - $totalRefundedAmount;
                }
                else {
                    $transaction['available_amount'] = 0;
                }
            }
            else if ($type == 'void') {
                $transaction['is_allowed'] = $this->model_payment_emerchantpay_direct->getTransactionsByTypeAndStatus($orderId, $transaction['unique_id'], 'void', 'approved') == false;
            }

            $url_action = $this->url->link(
                'payment/emerchantpay_direct/' . $type, 'token=' . $this->session->data['token'], 'SSL'
            );

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

                'emerchantpay_direct_supports_partial_capture'   => $this->config->get('emerchantpay_direct_supports_partial_capture'),
                'emerchantpay_direct_supports_partial_refund'    => $this->config->get('emerchantpay_direct_supports_partial_refund'),
                'emerchantpay_direct_supports_void'              => $this->config->get('emerchantpay_direct_supports_void')

            );

            $this->response->setOutput($this->load->view('payment/emerchantpay_direct_order_modal.tpl', $data));
        }
    }

    /**
     * Perform a Capture transaction
     *
     * @return void
     */
    public function capture()
    {
        $this->load->language('payment/emerchantpay_direct');

        if (isset($this->request->post['reference_id']) && trim($this->request->post['reference_id']) != '') {
            $this->load->model('payment/emerchantpay_direct');

            $transaction = $this->model_payment_emerchantpay_direct->getTransactionById($this->request->post['reference_id']);

            if (isset($transaction['order_id']) && abs((int)$transaction['order_id']) > 0) {
                $amount = $this->request->post['amount'];

                $message = isset($this->request->post['message']) ? $this->request->post['message'] : '';

                $capture = $this->model_payment_emerchantpay_direct->capture($transaction['unique_id'], $amount, $transaction['currency'], $message);

                if (isset($capture->unique_id)) {
                    $timestamp = ($capture->timestamp instanceof \DateTime)
                        ? $capture->timestamp->format('c')
                        : $capture->timestamp;

                    $data = array(
                        'order_id'          => $transaction['order_id'],
                        'reference_id'      => $transaction['unique_id'],
                        'unique_id'         => $capture->unique_id,
                        'type'              => $capture->transaction_type,
                        'mode'              => $capture->mode,
                        'status'            => $capture->status,
                        'amount'            => $capture->amount,
                        'currency'          => $capture->currency,
                        'timestamp'         => $timestamp,
                        'message'           => isset($capture->message) ? $capture->message : '',
                        'technical_message' => isset($capture->technical_message) ? $capture->technical_message : '',
                    );

                    $this->model_payment_emerchantpay_direct->populateTransaction($data);

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
     */
    public function refund()
    {
        $this->load->language('payment/emerchantpay_direct');

        if (isset($this->request->post['reference_id']) && trim($this->request->post['reference_id']) != '') {
            $this->load->model('payment/emerchantpay_direct');

            $transaction = $this->model_payment_emerchantpay_direct->getTransactionById($this->request->post['reference_id']);

            if (isset($transaction['order_id']) && intval($transaction['order_id']) > 0) {
                $amount = $this->request->post['amount'];

                $message = isset($this->request->post['message']) ? $this->request->post['message'] : '';

                $refund = $this->model_payment_emerchantpay_direct->refund($transaction['unique_id'], $amount, $transaction['currency'], $message);

                if (isset($refund->unique_id)) {
                    $timestamp = ($refund->timestamp instanceof \DateTime)
                        ? $refund->timestamp->format('c')
                        : $refund->timestamp;

                    $data = array(
                        'order_id'          => $transaction['order_id'],
                        'reference_id'      => $transaction['unique_id'],
                        'unique_id'         => $refund->unique_id,
                        'type'              => $refund->transaction_type,
                        'mode'              => $refund->mode,
                        'status'            => $refund->status,
                        'amount'            => $refund->amount,
                        'currency'          => $refund->currency,
                        'timestamp'         => $timestamp,
                        'message'           => isset($refund->message) ? $refund->message : '',
                        'technical_message' => isset($refund->technical_message) ? $refund->technical_message : '',
                    );

                    $this->model_payment_emerchantpay_direct->populateTransaction($data);

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
        $this->load->language('payment/emerchantpay_direct');

        if (isset($this->request->post['reference_id']) && trim($this->request->post['reference_id']) != '') {
            $this->load->model('payment/emerchantpay_direct');

            $transaction = $this->model_payment_emerchantpay_direct->getTransactionById($this->request->post['reference_id']);

            if (isset($transaction['order_id']) && abs((int)$transaction['order_id']) > 0) {
                $message = isset($this->request->post['message']) ? $this->request->post['message'] : '';

                $void = $this->model_payment_emerchantpay_direct->void($transaction['unique_id'], $message);

                if (isset($void->unique_id)) {
                    $timestamp = ($void->timestamp instanceof \DateTime)
                        ? $void->timestamp->format('c')
                        : $void->timestamp;

                    $data = array(
                        'order_id'          => $transaction['order_id'],
                        'reference_id'      => $transaction['unique_id'],
                        'unique_id'         => $void->unique_id,
                        'type'              => $void->transaction_type,
                        'mode'              => $void->mode,
                        'status'            => $void->status,
                        'amount'            => $void->amount,
                        'currency'          => $void->currency,
                        'timestamp'         => $timestamp,
                        'message'           => isset($void->message) ? $void->message : '',
                        'technical_message' => isset($void->technical_message) ? $void->technical_message : '',
                    );

                    $this->model_payment_emerchantpay_direct->populateTransaction($data);

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
        $this->load->model('payment/emerchantpay_direct');
        $this->model_payment_emerchantpay_direct->install();
    }

    /**
     * Remove/Uninstall Module Handling
     *
     * @return void
     */
    public function uninstall()
    {
        $this->load->model('payment/emerchantpay_direct');
        $this->model_payment_emerchantpay_direct->uninstall();
    }

    /**
     * Ensure that the current user has permissions to see/modify this module
     *
     * @return bool
     */
    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'payment/emerchantpay_direct')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (@empty($this->request->post['emerchantpay_direct_username'])) {
            $this->error['username'] = $this->language->get('error_username');
        }

        if (@empty($this->request->post['emerchantpay_direct_password'])) {
            $this->error['password'] = $this->language->get('error_password');
        }

        if (@empty($this->request->post['emerchantpay_direct_token'])) {
            $this->error['token'] = $this->language->get('error_token');
        }

        if (@empty($this->request->post['emerchantpay_direct_transaction_type'])) {
            $this->error['transaction_type'] = $this->language->get('error_transaction_type');
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
        $isLoggedIn = $this->user->isLogged();

        $hasAccess  = $this->user->hasPermission('access', 'payment/emerchantpay_direct');

        if (!$isLoggedIn || !$hasAccess) {
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
        if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off') {
            return true;
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            return true;
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_PORT']) && $_SERVER['HTTP_X_FORWARDED_PORT'] == '443') {
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
        return $this->session->data['currency'];
    }

    /**
     * Creates an array from a currency code, in order to be given to the template
     * @param string $currencyCode
     * @return array
     */
    protected function getTemplateCurrencyArray($currencyCode = null) {
        if (empty($currencyCode))
            $currencyCode = $this->getCurrencyCode();

        $this->load->model('localisation/currency');
        $currency = $this->model_localisation_currency->getCurrencyByCode($currencyCode);

        $currency_symbol = ($currency['symbol_left'] ? $currency['symbol_left'] : $currency['symbol_right']);
        if (empty($currency_symbol))
            $currency_symbol = $currency['code'];

        return $currency = array(
            'iso_code' 		    => $currency['code'],
            'sign' 		 		=> $currency_symbol,
            'decimalPlaces' 	=> 2,
            'decimalSeparator'  => '.',
            'thousandSeparator' => '' /* must be empty, otherwise exception could be trown from Genesis */
        );
    }

    /**
     * Add External Resources (JS & CSS)
     *
     * @param $resourceNames array
     *
     * @return bool
     */
    protected function addExternalResources($resourceNames) {
        $resourcesLoaded = (bool) count($resourceNames) > 0;

        foreach ($resourceNames as $resourceName)
            $resourcesLoaded = $this->addExternalResource($resourceName) && $resourcesLoaded;

        return $resourcesLoaded;
    }

    /**
     * Add External Resource (JS & CSS)
     *
     * @param $resourceName string
     *
     * @return bool
     */
    protected function addExternalResource($resourceName) {
        $resourceLoaded = true;

        if ($resourceName == 'treeGrid') {
            $this->document->addStyle('view/javascript/emerchantpay/treegrid/css/jquery.treegrid.css');
            $this->document->addScript('view/javascript/emerchantpay/treegrid/js/jquery.treegrid.js');
            $this->document->addScript('view/javascript/emerchantpay/treegrid/js/jquery.treegrid.bootstrap3.js');
        }
        else if ($resourceName == 'bootstrapValidator') {
            $this->document->addStyle('view/javascript/emerchantpay/bootstrap/css/bootstrapValidator.min.css');
            $this->document->addScript('view/javascript/emerchantpay/bootstrap/js/bootstrapValidator.min.js');
        }
        else if ($resourceName == 'bootstrapCheckbox') {
            $this->document->addScript('view/javascript/emerchantpay/bootstrap/js/bootstrap-checkbox.min.js');
        }
        else if ($resourceName == 'jQueryNumber') {
            $this->document->addScript('view/javascript/emerchantpay/jQueryExtensions/js/jquery.number.min.js');
        }
        else if ($resourceName == 'commonStyleSheet') {
            $this->document->addStyle('view/stylesheet/emerchantpay/emerchantpay-admin.css');
        }else
            $resourceLoaded = false;

        return $resourceLoaded;
    }

}