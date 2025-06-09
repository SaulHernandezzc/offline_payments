<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'tbl_offline_payments')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'tbl_offline_payments` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `clientid` INT NOT NULL,
        `invoiceid` INT NOT NULL,
        `bank` VARCHAR(100) NOT NULL,
        `document_type` VARCHAR(50) NOT NULL,
        `phone_prefix` VARCHAR(10) NOT NULL,
        `phone_number` VARCHAR(30) NOT NULL,
        `reference` VARCHAR(255) DEFAULT NULL,
        `attachment` VARCHAR(255) DEFAULT NULL,
        `status` VARCHAR(20) DEFAULT "pending",
        `date_submitted` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `admin_notes` TEXT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
}

if (!$CI->db->table_exists(db_prefix() . 'offline_payment_banks')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'offline_payment_banks` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(100) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
}

if (!$CI->db->table_exists(db_prefix() . 'paymentgateways')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'paymentgateways` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(100) DEFAULT NULL,
        `settings` LONGTEXT DEFAULT NULL,
        `active` TINYINT(1) DEFAULT 0,
        `fees` VARCHAR(50) DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
} else {
    // Add fees column if it does not exist
    $fields = $CI->db->list_fields(db_prefix() . 'paymentgateways');
    if (!in_array('fees', $fields)) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'paymentgateways` ADD `fees` VARCHAR(50) DEFAULT NULL;');
    }
}
// Ensure tblpaymentgateways has required columns
if (!$CI->db->field_exists('selected_by_default', db_prefix() . 'paymentgateways')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "paymentgateways` ADD `selected_by_default` TINYINT(1) DEFAULT 0;");
}

// Options
if (!get_option('offline_payment_email')) {
    add_option('offline_payment_email', '');
}
if (!get_option('offline_payment_display_name')) {
    add_option('offline_payment_display_name', 'Offline Payment');
}
if (!get_option('paymentmethod_offline_payment_active')) {
    add_option('paymentmethod_offline_payment_active', '0');
}
