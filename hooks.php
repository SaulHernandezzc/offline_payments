<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Menú Admin
hooks()->add_action('admin_init', 'offline_payment_module_add_admin_menu');

function offline_payment_module_add_admin_menu()
{
    $CI = &get_instance();

    if (is_admin()) {
        $CI->app_menu->add_sidebar_menu_item('offline_payment', [
            'name'     => _l('offline_payment'),
            'icon'     => 'fa fa-wallet',
            'position' => 35,
        ]);

        $CI->app_menu->add_sidebar_children_item('offline_payment', [
            'slug' => 'offline_payment_requests',
            'name' => _l('offline_payment_requests'),
            'href' => admin_url('offline_payment'),
        ]);

        $CI->app_menu->add_sidebar_children_item('offline_payment', [
            'slug' => 'offline_payment_banks',
            'name' => _l('offline_payment_manage_banks'),
            'href' => admin_url('offline_payment/banks'),
        ]);

        $CI->app_menu->add_sidebar_children_item('offline_payment', [
            'slug' => 'offline_payment_settings',
            'name' => _l('offline_payment_settings'),
            'href' => admin_url('offline_payment/settings'),
        ]);
    }
}

// ✅ REGISTRO DEL MÉTODO DE PAGO COMO GATEWAY
hooks()->add_filter('app_payment_gateways', function ($gateways) {
    $path = module_dir_path('offline_payment', 'libraries/Offline_payment_gateway.php');

    if (file_exists($path)) {
        require_once($path);

        if (class_exists('Offline_payment_gateway')) {
            $gateway = new Offline_payment_gateway();
            $gateway->setName(get_option('offline_payment_display_name') ?: 'Offline Payment');
            
            $gateways[] = [
                'id'       => $gateway->getId(),
                'name'     => $gateway->getName(),
                'instance' => $gateway,
            ];
        }
    }

    return $gateways;
});



// ✅ INSERCIÓN AUTOMÁTICA EN tblpaymentgateways SI NO EXISTE
hooks()->add_action('app_init', function () {
    $CI = &get_instance();
    $CI->load->database(); // Asegura que la DB está cargada

    $gateway = $CI->db->where('name', 'offline_payment')->get(db_prefix() . 'paymentgateways')->row();
    if (!$gateway) {
        $CI->db->insert(db_prefix() . 'paymentgateways', [
            'name'                => 'offline_payment',
            'display_name'        => 'Offline Payment',
            'fees'                => 0,
            'selected_by_default' => 0,
            'active'              => 1,
        ]);
    }
});

hooks()->add_action('app_init_payment_gateways', function () {
    $CI = &get_instance();

    $path = module_dir_path('offline_payment', 'libraries/Offline_payment_gateway.php');
    if (file_exists($path)) {
        require_once($path);

        if (class_exists('Offline_payment_gateway')) {
            $CI->gateways[$CI->gateways_index++] = new Offline_payment_gateway();
        }
    }
});



// ✅ INSERCIÓN AUTOMÁTICA EN tblpayment_modes SI NO EXISTE
hooks()->add_action('app_init', function () {
    $CI = &get_instance();
    $CI->load->database();

    $mode = $CI->db->where('name', 'Offline Payment')->get(db_prefix() . 'payment_modes')->row();
    if (!$mode) {
        $CI->db->insert(db_prefix() . 'payment_modes', [
            'name'                => 'Offline Payment',
    'description'         => 'Manual attachment via receipt',
    'active'              => 1,
    'show_on_pdf'         => 1,
    'selected_by_default' => 0,
    'type'                => 'online',
        ]);
    }
});
