<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Helper to return module base URL
 */
function offline_payment_url($uri = '')
{
    return admin_url('offline_payment' . ($uri ? '/' . ltrim($uri, '/') : ''));
}
