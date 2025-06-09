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

defined('OFFLINE_PAYMENT_MODULE_NAME') or define('OFFLINE_PAYMENT_MODULE_NAME', 'offline_payment');
defined('OFFLINE_GATEWAY_NAME') or define('OFFLINE_GATEWAY_NAME', 'offline_payment_gateway');

$CI = &get_instance();

// Cargar helper y language
$CI->load->helper(OFFLINE_PAYMENT_MODULE_NAME . '/' . OFFLINE_PAYMENT_MODULE_NAME);
register_language_files(OFFLINE_PAYMENT_MODULE_NAME, [OFFLINE_PAYMENT_MODULE_NAME]);

require_once(__DIR__ . '/hooks.php');