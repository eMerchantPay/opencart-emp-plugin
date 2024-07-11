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

/**
 * Base Abstract Class for Method Front Controllers
 *
 * Class ControllerExtensionPaymentEmerchantPayBase
 * @SuppressWarnings(PHPMD.LongClassName)
 */
abstract class ControllerExtensionPaymentEmerchantPayBase extends Controller
{


	/**
	 * OpenCart custom prefix
	 */
	const PLATFORM_TRANSACTION_PREFIX = 'ocart-';

	/**
	 * Module Name
	 *
	 * @var string
	 */
	protected $module_name = null;

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

			// Find theme directory
			if ($this->config->get('config_theme') == 'default') {
				$theme = $this->config->get('theme_default_directory');
			} else {
				$theme = $this->config->get('config_theme');
			}

			// set template directory
			$this->config->set('template_directory', $theme . '/template/');

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
	 * Override model route for 2.3.x compatibility
	 *
	 * @param $route
	 * @param $args
	 */
	public function overrideModelRoute(&$route, &$args) {

		// compatibility added in version 2.3.x is making route wrong when structure is made like in 3.0.x
		// simply adding method at the end fixes the problem
		if ($route == "payment/{$this->module_name}") {
			$route = "extension/{$route}/" . (empty($args) ? "recurringPayments" : "getMethod");
		}

	}

}
