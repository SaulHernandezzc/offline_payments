<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'offline_payment_requests')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'offline_payment_requests` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `invoice_id` INT NOT NULL,
        `bank` VARCHAR(100) NOT NULL,
        `document_type` VARCHAR(50) NOT NULL,
        `document_number` VARCHAR(100) NOT NULL,
        `phone_prefix` VARCHAR(10) NOT NULL,
        `phone_number` VARCHAR(30) NOT NULL,
        `reference` VARCHAR(255) DEFAULT NULL,
        `receipt` VARCHAR(255) DEFAULT NULL,
        `date_added` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `added_from` INT DEFAULT NULL,
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
    // Agregar columna 'fees' si no existe (por si ya exist¨ªa la tabla)
    $fields = $CI->db->list_fields(db_prefix() . 'paymentgateways');
    if (!in_array('fees', $fields)) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'paymentgateways` ADD `fees` VARCHAR(50) DEFAULT NULL;');
    }
}
// Asegura que la tabla tblpaymentgateways tenga las columnas requeridas

if (!$CI->db->field_exists('selected_by_default', db_prefix() . 'paymentgateways')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "paymentgateways` ADD `selected_by_default` TINYINT(1) DEFAULT 0;");
}


// Opciones de configuraci¨®n
if (!get_option('offline_payment_email')) {
    add_option('offline_payment_email', '');
}
if (!get_option('offline_payment_display_name')) {
    add_option('offline_payment_display_name', 'Offline Payment');
}
if (!get_option('paymentmethod_offline_payment_active')) {
    add_option('paymentmethod_offline_payment_active', '0'); // por defecto desactivado
}
