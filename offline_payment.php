<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Offline Payment
Description: Allows clients to attach a payment receipt manually for invoice processing.
Version: 1.0
Requires at least: 3.0.0
Author: GPS Tracker International
Author URI: https://trackerinternationalinc.com
*/

defined('OFFLINE_PAYMENT_MODULE_NAME') or define('OFFLINE_PAYMENT_MODULE_NAME', 'offline_payments');
defined('OFFLINE_GATEWAY_NAME') or define('OFFLINE_GATEWAY_NAME', 'offline_payment_gateway');

$CI = &get_instance();

// Load helper and language
// The helper file is named "offline_payment_helper.php" and sits directly under
// the helpers directory. The previous implementation attempted to load a helper
// named "offline_payments_helper.php", which does not exist and caused a
// "Unable to load the requested file" error. Load the correct helper instead.
$CI->load->helper(OFFLINE_PAYMENT_MODULE_NAME . '/offline_payment');
register_language_files(OFFLINE_PAYMENT_MODULE_NAME, [OFFLINE_PAYMENT_MODULE_NAME]);

require_once __DIR__ . '/hooks.php';
