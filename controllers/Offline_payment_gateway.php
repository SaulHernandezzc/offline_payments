<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Offline_payment_gateway extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('offline_payment_model');

        // ✅ Carga correcta del idioma
        $this->lang->load('offline_payment', 'english');
    }

    public function form($invoice_id = null)
    {
        if (!is_client_logged_in()) {
            redirect(site_url('authentication/login'));
        }

        if (!$invoice_id && $this->input->get('invoice_id')) {
            $invoice_id = $this->input->get('invoice_id');
        }

        if (!$invoice_id || !is_numeric($invoice_id)) {
            show_404();
        }

        $invoice = $this->invoices_model->get($invoice_id);
        if (!$invoice || $invoice->clientid != get_client_user_id()) {
            show_404();
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $success = $this->offline_payment_model->save_client_payment($data, $invoice_id);
            echo json_encode(['success' => $success]);
            die;
        }

        $data['invoice'] = $invoice;
        $data['title'] = _l('offline_payment');
        $data['banks'] = $this->offline_payment_model->get_banks();

        $this->data($data);
        $this->view('form');
        $this->layout();
    }
}
