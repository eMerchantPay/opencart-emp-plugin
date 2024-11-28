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
?>

<?php echo $header; ?><?php echo $column_left; ?>

<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <span class="form-loading">
                    <i class="fa fa-spinner fa-spin fa-lg"></i>
                </span>
                <button type="button" id="<?php echo $module_name;?>_submit" data-form="form-emerchantpay_checkout" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid module-controls-container">
        <div class="alert alert-notification alert-dismissible">
            <i class="fa fa-info-circle"></i>
            <span class="alert-text"></span>
            <button type="button" class="close" data-hide="alert-notification">&times;</button>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-pencil"></i>&nbsp;<?php echo $text_edit; ?>
                    &nbsp;
                    <strong>(v. <?php echo $module_version;?>)</strong>
                </h3>
            </div>
            <div class="panel-body">
                <form data-action="<?php echo $action; ?>" data-method="post" enctype="multipart/form-data" id="form-emerchantpay_checkout" class="form-horizontal">
                    <ul class="nav nav-tabs" id="tabs">
                        <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
                        <li class="<?php if (!$enable_recurring_tab) echo 'hidden'; ?>"><a href="#tab-recurring" data-toggle="tab"><?php echo $tab_recurring; ?></a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-general">
                            <div class="form-group required">
                                <label class="col-sm-2 control-label" for="input-account">
                                    <?php echo $entry_username; ?>
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" id="<?php echo $module_name;?>_username" name="<?php echo $module_name;?>_username" value="<?php echo $emerchantpay_checkout_username; ?>" placeholder="<?php echo $entry_username; ?>" id="input-account" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group required">
                                <label class="col-sm-2 control-label" for="input-secret">
                                    <?php echo $entry_password; ?>
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" id="<?php echo $module_name;?>_password" name="<?php echo $module_name;?>_password" value="<?php echo $emerchantpay_checkout_password; ?>" placeholder="<?php echo $entry_password; ?>" id="input-secret" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                            <span data-toggle="tooltip" title="<?php echo $help_sandbox; ?>">
                                <?php echo $entry_sandbox; ?>
                            </span>
                                </label>
                                <div class="col-sm-10 bootstrap-checkbox-holder">
                                    <input type="hidden" name="emerchantpay_checkout_sandbox"
                                           value="<?php echo $emerchantpay_checkout_sandbox;?>" />
                                    <input type="checkbox" class="bootstrap-checkbox"
                                    <?php if ($emerchantpay_checkout_sandbox) { ?>
                                    checked="checked"
                                    <?php } ?>
                                    />
                                </div>
                            </div>
                            <div class="form-group required">
                                <label class="col-sm-2 control-label" for="input-order-status">
                                    <?php echo $entry_transaction_type; ?>
                                </label>
                                <div class="col-sm-10">
                                    <select id="<?php echo $module_name;?>_transaction_type" name="<?php echo $module_name;?>_transaction_type[]" id="input-order-status" class="form-control" multiple="multiple">
                                        <?php foreach ($transaction_types as $transaction_type) { ?>
                                        <?php if (is_array($emerchantpay_checkout_transaction_type) && in_array($transaction_type['id'], $emerchantpay_checkout_transaction_type)) { ?>
                                        <option value="<?php echo $transaction_type['id']; ?>" selected="selected"><?php echo $transaction_type['name']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $transaction_type['id']; ?>"><?php echo $transaction_type['name']; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="<?php echo $module_name;?>_bank_codes">
                                    <?php echo $entry_bank_codes; ?>
                                </label>
                                <div class="col-sm-10">
                                    <select id="<?php echo $module_name;?>_bank_codes" name="<?php echo $module_name;?>_bank_codes[]" class="form-control" multiple="multiple">
                                        <?php foreach ($bank_codes as $bank_code) { ?>
                                        <?php if (is_array($emerchantpay_checkout_bank_codes) && in_array($bank_code['id'], $emerchantpay_checkout_bank_codes)) { ?>
                                        <option value="<?php echo $bank_code['id']; ?>" selected="selected"><?php echo $bank_code['name']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $bank_code['id']; ?>"><?php echo $bank_code['name']; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-status">
                                    <?php echo $entry_wpf_tokenization; ?>
                                </label>
                                <div class="col-sm-10 bootstrap-checkbox-holder">
                                    <input type="hidden" name="emerchantpay_checkout_wpf_tokenization"
                                           value="<?php echo $emerchantpay_checkout_wpf_tokenization;?>" />
                                    <input type="checkbox" class="bootstrap-checkbox"
                                    <?php if ($emerchantpay_checkout_wpf_tokenization) { ?>
                                    checked="checked"
                                    <?php } ?>
                                    />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="emerchantpay_checkout_threeds_allowed">
                                    <span data-toggle="tooltip" title="<?php echo $help_threeds_allowed; ?>">
                                        <?php echo $entry_threeds_allowed; ?>
                                    </span>
                                </label>
                                <div class="col-sm-10 bootstrap-checkbox-holder">
                                    <input type="hidden" name="emerchantpay_checkout_threeds_allowed"
                                           value="<?php echo $emerchantpay_checkout_threeds_allowed;?>" />
                                    <input type="checkbox" class="bootstrap-checkbox" id="emerchantpay_checkout_threeds_allowed"
                                    <?php if ($emerchantpay_checkout_threeds_allowed) { ?>
                                    checked="checked"
                                    <?php } ?>
                                    />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="emerchantpay_checkout_threeds_challenge_indicator">
                                    <span data-toggle="tooltip" title="<?php echo $help_threeds_challenge_indicator; ?>">
                                        <?php echo $entry_threeds_challenge_indicator; ?>
                                    </span>
                                </label>
                                <div class="col-sm-10">
                                    <select name="emerchantpay_checkout_threeds_challenge_indicator" id="emerchantpay_checkout_threeds_challenge_indicator" class="form-control">
                                        <?php foreach ($threeds_challenge_indicators as $threeds_challenge_indicator) { ?>
                                        <?php if ($threeds_challenge_indicator['id'] == $emerchantpay_checkout_threeds_challenge_indicator) { ?>
                                        <option value="<?php echo $threeds_challenge_indicator['id']; ?>" selected="selected"><?php echo $threeds_challenge_indicator['name']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $threeds_challenge_indicator['id']; ?>"><?php echo $threeds_challenge_indicator['name']; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="emerchantpay_checkout_sca_exemption">
                                    <span data-toggle="tooltip" title="<?php echo $help_sca_exemption; ?>">
                                        <?php echo $entry_sca_exemption; ?>
                                    </span>
                                </label>
                                <div class="col-sm-10">
                                    <select name="emerchantpay_checkout_sca_exemption" id="emerchantpay_checkout_sca_exemption" class="form-control">
                                        <?php foreach ($sca_exemptions as $sca_exemption) { ?>
                                        <?php $selected = ($sca_exemption['id'] == $emerchantpay_checkout_sca_exemption) ? ' selected="selected" ' : ''; ?>
                                        <option value="<?php echo $sca_exemption['id']; ?>"<?php echo $selected; ?>>
                                            <?php echo $sca_exemption['name']; ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-sca_exemption_amount">
                            <span data-toggle="tooltip" title="<?php echo $help_sca_exemption_amount; ?>">
                                <?php echo $entry_sca_exemption_amount; ?>
                            </span>
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" name="emerchantpay_checkout_sca_exemption_amount" value="<?php echo $emerchantpay_checkout_sca_exemption_amount; ?>" placeholder="<?php echo $entry_sca_exemption_amount; ?>" id="input-sca_exemption_amount" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                            <span data-toggle="tooltip" title="<?php echo $help_supports_partial_capture; ?>">
                                <?php echo $entry_supports_partial_capture;?>
                            </span>
                                </label>
                                <div class="col-sm-10 bootstrap-checkbox-holder">
                                    <input type="hidden" name="emerchantpay_checkout_supports_partial_capture"
                                           value="<?php echo $emerchantpay_checkout_supports_partial_capture;?>" />
                                    <input type="checkbox" class="bootstrap-checkbox"
                                    <?php if ($emerchantpay_checkout_supports_partial_capture) { ?>
                                    checked="checked"
                                    <?php } ?>
                                    />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                            <span data-toggle="tooltip" title="<?php echo $help_supports_partial_refund; ?>">
                                <?php echo $entry_supports_partial_refund;?>
                            </span>
                                </label>
                                <div class="col-sm-10 bootstrap-checkbox-holder">
                                    <input type="hidden" name="emerchantpay_checkout_supports_partial_refund"
                                           value="<?php echo $emerchantpay_checkout_supports_partial_refund;?>" />
                                    <input type="checkbox" class="bootstrap-checkbox"
                                    <?php if ($emerchantpay_checkout_supports_partial_refund) { ?>
                                    checked="checked"
                                    <?php } ?>
                                    />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                            <span data-toggle="tooltip" title="<?php echo $help_supports_void; ?>">
                                <?php echo $entry_supports_void;?>
                            </span>
                                </label>
                                <div class="col-sm-10 bootstrap-checkbox-holder">
                                    <input type="hidden" name="emerchantpay_checkout_supports_void"
                                           value="<?php echo $emerchantpay_checkout_supports_void; ?>" />
                                    <input type="checkbox" class="bootstrap-checkbox"
                                    <?php if ($emerchantpay_checkout_supports_void) { ?>
                                    checked="checked"
                                    <?php } ?>
                                    />
                                </div>
                            </div>
                            <div class="form-group required">
                                <label class="col-sm-2 control-label" for="emerchantpay_checkout_order_status_id">
                            <span data-toggle="tooltip" title="<?php echo $help_order_status; ?>">
                                <?php echo $entry_order_status; ?>
                            </span>
                                </label>
                                <div class="col-sm-10">
                                    <select name="emerchantpay_checkout_order_status_id" id="emerchantpay_checkout_order_status_id" class="form-control">
                                        <option value="" >- <?php echo $text_select_status; ?> -</option>
                                        <?php foreach ($order_statuses as $order_status) { ?>
                                        <?php if ($order_status['order_status_id'] == $emerchantpay_checkout_order_status_id) { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group required">
                                <label class="col-sm-2 control-label" for="emerchantpay_checkout_order_failure_status_id">
                            <span data-toggle="tooltip" title="<?php echo $help_failure_order_status; ?>">
                                <?php echo $entry_order_status_failure; ?>
                            </span>
                                </label>
                                <div class="col-sm-10">
                                    <select name="emerchantpay_checkout_order_failure_status_id" id="emerchantpay_checkout_order_failure_status_id" class="form-control">
                                        <option value="" >- <?php echo $text_select_status; ?> -</option>
                                        <?php foreach ($order_statuses as $order_status) { ?>
                                        <?php if ($order_status['order_status_id'] == $emerchantpay_checkout_order_failure_status_id) { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                            <span data-toggle="tooltip" title="<?php echo $help_order_email_create; ?>">
                                <?php echo $entry_order_email_create;?>
                            </span>
                                </label>
                                <div class="col-sm-10 bootstrap-checkbox-holder">
                                    <input type="hidden" name="emerchantpay_checkout_order_email_create"
                                           value="<?php echo $emerchantpay_checkout_order_email_create;?>" />
                                    <input type="checkbox" class="bootstrap-checkbox"
                                    <?php if ($emerchantpay_checkout_order_email_create) { ?>
                                    checked="checked"
                                    <?php } ?>
                                    />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                            <span data-toggle="tooltip" title="<?php echo $help_order_email_payment_failure; ?>">
                                <?php echo $entry_order_email_payment_failure;?>
                            </span>
                                </label>
                                <div class="col-sm-10 bootstrap-checkbox-holder">
                                    <input type="hidden" name="emerchantpay_checkout_order_email_payment_failure"
                                           value="<?php echo $emerchantpay_checkout_order_email_payment_failure;?>" />
                                    <input type="checkbox" class="bootstrap-checkbox"
                                    <?php if ($emerchantpay_checkout_order_email_payment_failure) { ?>
                                    checked="checked"
                                    <?php } ?>
                                    />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-total">
                            <span data-toggle="tooltip" title="<?php echo $help_total; ?>">
                                <?php echo $entry_total; ?>
                            </span>
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" name="emerchantpay_checkout_total" value="<?php echo $emerchantpay_checkout_total; ?>" placeholder="<?php echo $entry_total; ?>" id="input-total" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-geo-zone">
                                    <?php echo $entry_geo_zone; ?>
                                </label>
                                <div class="col-sm-10">
                                    <select name="emerchantpay_checkout_geo_zone_id" id="input-geo-zone" class="form-control">
                                        <option value="0"><?php echo $text_all_zones; ?></option>
                                        <?php foreach ($geo_zones as $geo_zone) { ?>
                                        <?php if ($geo_zone['geo_zone_id'] == $emerchantpay_checkout_geo_zone_id) { ?>
                                        <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-status">
                                    <?php echo $entry_status; ?>
                                </label>
                                <div class="col-sm-10 bootstrap-checkbox-holder">
                                    <input type="hidden" name="emerchantpay_checkout_status"
                                           value="<?php echo $emerchantpay_checkout_status;?>" />
                                    <input type="checkbox" class="bootstrap-checkbox"
                                    <?php if ($emerchantpay_checkout_status) { ?>
                                    checked="checked"
                                    <?php } ?>
                                    />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-status">
                                    <?php echo $entry_debug; ?>
                                </label>
                                <div class="col-sm-10 bootstrap-checkbox-holder">
                                    <input type="hidden" name="emerchantpay_checkout_debug"
                                           value="<?php echo $emerchantpay_checkout_debug;?>" />
                                    <input type="checkbox" class="bootstrap-checkbox"
                                    <?php if ($emerchantpay_checkout_debug) { ?>
                                    checked="checked"
                                    <?php } ?>
                                    />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-sort-order">
                                    <?php echo $entry_sort_order; ?>
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" name="emerchantpay_checkout_sort_order" value="<?php echo $emerchantpay_checkout_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab-recurring">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                            <span data-toggle="tooltip" title="<?php echo $help_supports_recurring; ?>">
                                <?php echo $entry_supports_recurring;?>
                            </span>
                                </label>
                                <div class="col-sm-10 bootstrap-checkbox-holder">
                                    <input type="hidden" name="emerchantpay_checkout_supports_recurring"
                                           value="<?php echo $emerchantpay_checkout_supports_recurring; ?>" />
                                    <input type="checkbox" class="bootstrap-checkbox"
                                    <?php if ($emerchantpay_checkout_supports_recurring) { ?>
                                    checked="checked"
                                    <?php } ?>
                                    />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                                    <span data-toggle="tooltip" title="<?php echo $help_recurring_transaction_types; ?>">
                                        <?php echo $entry_recurring_transaction_type; ?>
                                    </span>
                                </label>
                                <div class="col-sm-10">
                                    <select id="<?php echo $module_name;?>_recurring_transaction_type" name="<?php echo $module_name;?>_recurring_transaction_type[]" class="form-control" multiple="multiple">
                                        <?php foreach ($recurring_transaction_types as $recurring_transaction_type) { ?>
                                        <?php if (is_array($emerchantpay_checkout_recurring_transaction_type) && in_array($recurring_transaction_type['id'], $emerchantpay_checkout_recurring_transaction_type)) { ?>
                                        <option value="<?php echo $recurring_transaction_type['id']; ?>" selected="selected"><?php echo $recurring_transaction_type['name']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $recurring_transaction_type['id']; ?>"><?php echo $recurring_transaction_type['name']; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                                    <?php echo $entry_recurring_token; ?>
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" id="emerchantpay_checkout_recurring_token" name="emerchantpay_checkout_recurring_token" value="<?php echo $emerchantpay_checkout_recurring_token; ?>" placeholder="<?php echo $entry_recurring_token; ?>" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                                        <span data-toggle="tooltip" title="<?php echo $help_cron_time_limit; ?>">
                                        <?php echo $entry_cron_time_limit; ?>
                                    </span>
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" id="emerchantpay_checkout_cron_time_limit" name="emerchantpay_checkout_cron_time_limit" value="<?php echo $emerchantpay_checkout_cron_time_limit; ?>" placeholder="<?php echo $entry_cron_time_limit; ?>" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                                        <span data-toggle="tooltip" title="<?php echo $help_cron_allowed_ip; ?>">
                                        <?php echo $entry_cron_allowed_ip; ?>
                                    </span>
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" id="emerchantpay_checkout_cron_allowed_ip" name="emerchantpay_checkout_cron_allowed_ip" value="<?php echo $emerchantpay_checkout_cron_allowed_ip; ?>" placeholder="<?php echo $entry_cron_allowed_ip; ?>" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                                    <span data-toggle="tooltip" title="<?php echo $help_cron_last_execution; ?>">
                                        <?php echo $entry_cron_last_execution; ?>
                                    </span>
                                </label>
                                <div class="col-sm-10">
                                    <div id="div_cron_last_execution"><?php echo $cron_last_execution; ?></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                            <span data-toggle="tooltip" title="<?php echo $help_recurring_log; ?>">
                                <?php echo $entry_recurring_log; ?>
                            </span>
                                </label>
                                <div class="col-sm-10">
                                    <div>
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" class="button btn btn-info" id="btn_collapse">
                                            <?php echo $text_log_btn_show; ?>
                                        </a>
                                    </div>
                                    <div id="collapseOne" class="panel-collapse collapse out">
                                        <table class="table table-hover tree">
                                            <thead>
                                            <tr>
                                                <th><?php echo $text_log_entry_id; ?></th>
                                                <th class="text-center"><?php echo $text_log_order_id; ?></th>
                                                <th class="text-center"><?php echo $text_log_date_time; ?></th>
                                                <th class="text-center"><?php echo $text_log_rebilled_amount; ?></th>
                                                <th class="text-center"><?php echo $text_log_recurring_order_id; ?></th>
                                                <th class="text-center"><?php echo $text_log_status; ?></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($recurring_log_entries as $log_entry) { ?>
                                            <tr class="treegrid-<?php echo $log_entry['log_entry_id'];?> <?php echo $log_entry['ref_log_entry_id']!='' ? 'treegrid-parent-' . $log_entry['ref_log_entry_id'] : 'log-entry-row'; ?>">
                                                <td class="text-left"><?php echo $log_entry['log_entry_id']; ?></td>
                                                <td class="text-center"><a href="<?php echo $log_entry['order_link']; ?>" data-toggle="tooltip" title="<?php echo $log_entry['order_link_title']; ?>"><?php echo $log_entry['order_id']; ?></a></td>
                                                <td class="text-center"><?php echo $log_entry['date']; ?></td>
                                                <td class="text-center"><?php echo $log_entry['amount']; ?></td>
                                                <td class="text-center">
                                                    <?php if (array_key_exists('order_recurring_btn_title', $log_entry) && $log_entry['order_recurring_btn_title'] != '') { ?>
                                                    <a href="<?php echo $log_entry['order_recurring_btn_link']; ?>" data-toggle="tooltip" title="<?php echo $log_entry['order_recurring_btn_title']; ?>" class="btn btn-info"><i class="fa fa-eye"></i></a>
                                                    <?php } else { ?>
                                                    <?php echo $log_entry['order_recurring_id']; ?>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-center"><?php echo $log_entry['status']; ?></td>
                                            </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>

<script type="text/javascript">

    function createBootstrapValidator(submitFormSelector) {
        var submitForm = $(submitFormSelector);

        submitForm.bootstrapValidator({
            fields: {
                username: {
                    selector: '#<?php echo $module_name;?>_username',
                    validators: {
                        notEmpty: {
                            message: '<?php echo $error_username;?>'
                        }
                    }
                },
                password: {
                    selector: '#<?php echo $module_name;?>_password',
                    validators: {
                        notEmpty: {
                            message: '<?php echo $error_password;?>'
                        }
                    }
                },
                transactionType: {
                    selector: '#<?php echo $module_name;?>_transaction_type',
                    trigger: 'click', /*it is a little bit slow for multiselect combobox without trigger */
                    validators: {
                        notEmpty: {
                            message: '<?php echo $error_transaction_type;?>'
                        }
                    }
                },
                orderStatus: {
                    selector: '#<?php echo $module_name;?>_order_status_id',
                    validators: {
                        notEmpty: {
                            message: '<?php echo $error_order_status;?>'
                        }
                    }
                },
                orderFailureStatus: {
                    selector: '#<?php echo $module_name;?>_order_failure_status_id',
                    validators: {
                        notEmpty: {
                            message: '<?php echo $error_order_failure_status;?>'
                        }
                    }
                }
            }
        })
        .on('success.form.bv', function(e) {
            e.preventDefault(); // Prevent the form from submitting
        });

        return true;
    }

    function destroyBootstrapValidator(submitFormSelector) {
        var submitForm = $(submitFormSelector);
        submitForm.bootstrapValidator('destroy');
    }

    function hideAlertNotification() {
        var $alertNotificationHolder = $('.module-controls-container').find('.alert-notification');
        $alertNotificationHolder.slideUp();
    }

    function displayAlertNotification(type, messageText) {
        var $alertNotificationHolder = $('.module-controls-container').find('.alert-notification');
        var alertNotificationClass = 'alert-' + type;
        var notificationTypes = [
            'info',
            'success',
            'warning',
            'danger'
        ];

        $alertNotificationHolder.find('.alert-text').html(messageText);

        $.each(notificationTypes, function(index, key) {
            $alertNotificationHolder.removeClass('alert-' + key);
        });

        $alertNotificationHolder.addClass(alertNotificationClass).slideDown();
    }

    function updateCronLastExecClass() {
        $("#div_cron_last_execution").attr('class', 'alert alert-<?php echo $cron_last_execution_status; ?>');
    }

    $(function() {

        destroyBootstrapValidator('#form-emerchantpay_checkout');
        createBootstrapValidator('#form-emerchantpay_checkout');

        $("[data-hide]").on("click", function(){
            $("." + $(this).attr("data-hide")).slideUp();
        });

        $('#<?php echo $module_name;?>_submit').click(function() {
            var $submitForm = $('#' + $(this).attr('data-form'));
            $submitForm.submit();
        });

        $('#form-emerchantpay_checkout').submit(function() {
            var $form = $(this);

            hideAlertNotification();

            $.ajax({
                url:    $form.attr('data-action'),
                type:   $form.attr('data-method'),
                data:   $form.serialize(),
                beforeSend: function () {
                    $('#<?php echo $module_name;?>_submit').attr('disabled', 'disabled');
                    $('#<?php echo $module_name;?>_submit').parent().find('.form-loading').fadeIn('fast');
                },
                complete: function() {
                    $('#<?php echo $module_name;?>_submit').parent().find('.form-loading').fadeOut('fast');
                    $('#<?php echo $module_name;?>_submit').removeAttr('disabled');
                },
                success: function (data) {
                    if (data.success == 1)
                        displayAlertNotification('success', data.text);
                    else if (data.text !== undefined && data.text.length > 0) {
                        displayAlertNotification('danger', data.text);
                    }
                    else {
                        displayAlertNotification('danger', '<?php echo $text_failed;?>');
                    }
                },
                error: function(xhr) {
                    displayAlertNotification('danger', '<?php echo $text_failed;?>');
                }
            });

            // prevent re-submitting
            return false;
        });

        $('input.bootstrap-checkbox').checkboxpicker({
            html: false,
            offLabel: '<?php echo $text_no;?>',
            onLabel: '<?php echo $text_yes;?>',
            style: 'btn-group-sm'
        });

        $('input.bootstrap-checkbox').change(function() {
            var isChecked = $(this).prop('checked');
            $(this).parent().find('input[type="hidden"]').val((isChecked ? 1 : 0));

            if ($(this).parent().find('input[type="hidden"]').attr('name') == 'emerchantpay_checkout_supports_recurring')
            {
                if (!isChecked)
                {
                    displayAlertNotification('warning', '<?php echo $alert_disable_recurring; ?>');
                }
            }
        });

        $('.tree').treegrid({
            expanderExpandedClass:  'treegrid-expander-expanded',
            expanderCollapsedClass: 'treegrid-expander-collapsed'
        });

        updateCronLastExecClass();
    });

    $('#collapseOne').on('hidden.bs.collapse', function () {
        document.getElementById('btn_collapse').textContent="<?php echo $text_log_btn_show; ?>";
    });

    $('#collapseOne').on('shown.bs.collapse', function () {
        document.getElementById('btn_collapse').textContent='<?php echo $text_log_btn_hide; ?>';
    });

</script>
