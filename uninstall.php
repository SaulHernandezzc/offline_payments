<?php defined('BASEPATH') or exit('No direct script access allowed');

$CI =& get_instance();
$CI->db->query('DROP TABLE IF EXISTS ' . db_prefix() . 'offline_payments');
$CI->db->query('DROP TABLE IF EXISTS ' . db_prefix() . 'offline_payment_banks');

delete_option('offline_payment_email');
delete_option('offline_payment_display_name');
delete_option('paymentmethod_offline_payment_active');
