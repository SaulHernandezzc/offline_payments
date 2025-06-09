<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Offline_payment_gateway extends App_gateway
{
    protected $ci;

    public function __construct()
    {
        parent::__construct();

        $this->ci =& get_instance();

        $this->setId('offline_payment');

        // Nombre del método de pago configurable desde settings
        $this->setName(get_option('paymentmethod_offline_payment_label') ?: 'Offline Payment');

        // Ajustes mostrados en la configuración del método de pago
        $this->setSettings([
            ['name' => 'active', 'label' => _l('settings_yes'), 'type' => 'checkbox'],
        ]);
    }

    // Forzar comportamiento como método online
    public function is_online()
    {
        return true;
    }

    public function getType()
    {
        return 'online';
    }

    // Mostrar este gateway al cliente siempre
    public function is_gateway_visible_to_customer()
    {
        return true;
    }

    // Vista del formulario del cliente
    public function get_view($invoice)
    {
        $this->ci->load->model('offline_payment_model');

        return module_views_path('offline_payment', 'gateway_offline_payment', [
            'invoice' => $invoice,
            'banks'   => $this->ci->offline_payment_model->get_banks(),
        ]);
    }

    // Procesamiento del pago offline
    public function process_payment($data)
    {
        $invoice = $data['invoice'];
        $invoice_id = $invoice->id;

        if (!$this->ci->input->post()) {
            return ['success' => false, 'message' => _l('offline_payment_submission_failed')];
        }

        $uploaded_file = handle_file_upload('attachment', 'offline_payment_receipts', null, 'image');
        $file_name     = isset($uploaded_file[0]['file_name']) ? $uploaded_file[0]['file_name'] : null;

        $payment_data = [
            'clientid'       => get_client_user_id(),
            'invoiceid'      => $invoice_id,
            'bank'           => $this->ci->input->post('bank'),
            'document_type'  => $this->ci->input->post('document_type'),
            'phone_prefix'   => $this->ci->input->post('phone_prefix'),
            'phone_number'   => $this->ci->input->post('phone_number'),
            'reference'      => $this->ci->input->post('reference'),
            'attachment'     => $file_name,
            'status'         => 'paid',
            'date_submitted' => date('Y-m-d H:i:s'),
        ];

        $this->ci->load->model('offline_payment_model');
        $inserted_id = $this->ci->offline_payment_model->add_payment($payment_data);

        if ($inserted_id) {
            $this->ci->offline_payment_model->send_admin_notification($inserted_id, $invoice_id);
            return ['success' => true];
        }

        return ['success' => false, 'message' => _l('offline_payment_submission_failed')];
    }
}
