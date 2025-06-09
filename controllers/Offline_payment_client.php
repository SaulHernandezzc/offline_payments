<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Offline_payment_client extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('offline_payment_model');

        $language = $this->config->item('language');
        if (!file_exists(module_dir_path(OFFLINE_PAYMENT_MODULE_NAME, "language/$language/offline_payment_lang.php"))) {
            $language = 'english';
        }

        $this->lang->load('offline_payment', $language);
    }
    
    public function index()
{
    $invoice_id = $this->input->get('invoice_id');

    if (!$invoice_id) {
        show_404();
    }

    $data['invoice'] = $this->invoices_model->get($invoice_id);

    if (!$data['invoice']) {
        show_404();
    }

    $data['banks'] = $this->offline_payment_model->get_banks();
    $data['title'] = _l('offline_payment');

    $this->load->view('themes/' . active_clients_theme() . '/offline_payment_form', $data);
}

    public function submit($invoice_id)
    {
        if ($this->input->post()) {
            $clientid = get_client_user_id();

            // Manejo del archivo de comprobante
            $uploaded_file = handle_file_upload(
                'attachment',
                'offline_payment_receipts',
                null,
                'image'
            );

            $file_name = isset($uploaded_file[0]['file_name']) ? $uploaded_file[0]['file_name'] : null;

            $data = [
                'clientid'       => $clientid,
                'invoiceid'      => $invoice_id,
                'bank'           => $this->input->post('bank'),
                'document_type'  => $this->input->post('document_type'),
                'phone_prefix'   => $this->input->post('phone_prefix'),
                'phone_number'   => $this->input->post('phone_number'),
                'reference'      => $this->input->post('reference'),
                'attachment'     => $file_name,
                'status'         => 'paid',
                'date_submitted' => date('Y-m-d H:i:s'),
            ];

            $payment_id = $this->offline_payment_model->add_payment($data);

            if ($payment_id) {
                // ✅ Enviar email
                $this->send_payment_email($payment_id);

                set_alert('success', _l('offline_payment_sent_successfully'));
                redirect(site_url('clients/invoices/' . $invoice_id));
            } else {
                set_alert('danger', _l('offline_payment_submission_failed'));
            }
        }

        // Renderiza el formulario (debes tener la vista)
        $data['invoice_id'] = $invoice_id;
        $data['banks'] = $this->offline_payment_model->get_banks();
        $data['title'] = _l('submit_offline_payment');
        $this->load->view('client_submit', $data);
    }

    private function send_payment_email($payment_id)
    {
        $payment = $this->offline_payment_model->get_payment($payment_id);

        if (!$payment) {
            return false;
        }

        $client_name    = get_company_name($payment['clientid']);
        $invoice_id     = $payment['invoiceid'];
        $bank           = $payment['bank'];
        $reference      = $payment['reference'];
        $phone_prefix   = $payment['phone_prefix'];
        $phone_number   = $payment['phone_number'];
        $date_submitted = $payment['date_submitted'];

        $email_body = $this->load->view('email_template', [
            'client_name'    => $client_name,
            'invoice_id'     => $invoice_id,
            'bank'           => $bank,
            'reference'      => $reference,
            'phone_prefix'   => $phone_prefix,
            'phone_number'   => $phone_number,
            'date_submitted' => $date_submitted,
        ], true);

        $to         = get_option('offline_payment_email');
        $subject    = 'New Offline Payment Submitted';
        $attachments = [];

        if (!empty($payment['attachment'])) {
            $path = FCPATH . 'uploads/offline_payment_receipts/' . $payment['id'] . '/' . $payment['attachment'];
            if (file_exists($path)) {
                $attachments[] = $path;
            }
        }

        $sent = send_mail($to, $subject, $email_body, '', '', $attachments);

        if ($sent) {
            log_activity('Offline payment email sent for Payment ID: ' . $payment_id);
        }

        return $sent;
    }
}
