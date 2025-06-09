<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Offline_payment extends AdminController
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

        if (!is_admin()) {
            access_denied('Offline Payment');
        }
    }

    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('offline_payment_table');
            return;
        }

        // Si se accede como cliente con invoice_id
        $invoice_id = $this->input->get('invoice_id');
        if ($invoice_id) {
            if (!is_numeric($invoice_id)) {
                redirect(site_url('clients/invoices'));
            }

            $data['invoice'] = $this->invoices_model->get($invoice_id);
            if (!$data['invoice']) {
                redirect(site_url('clients/invoices'));
            }

            $data['banks'] = $this->offline_payment_model->get_banks();
            $data['title'] = _l('offline_payment');

            $this->load->view('themes/' . active_clients_theme() . '/views/offline_payment_form', $data);
            return;
        }

        // Vista admin
        $filters = [];

        if ($this->input->get('status')) {
            $filters['status'] = $this->input->get('status');
        }

        if ($this->input->get('bank')) {
            $filters['bank'] = $this->input->get('bank');
        }

        $data['title']      = _l('offline_payment_requests');
        $data['payments']   = $this->offline_payment_model->get_all_payments($filters);
        $data['all_banks']  = $this->offline_payment_model->get_banks();
        $data['dashboard']  = $this->offline_payment_model->get_dashboard_metrics($filters);

        $this->load->view('manage', $data);
    }

    public function settings()
    {
        if ($this->input->post()) {
            $email        = trim($this->input->post('offline_payment_email'));
            $display_name = trim($this->input->post('offline_payment_display_name'));
            $label_name   = trim($this->input->post('paymentmethod_offline_payment_label'));

            update_option('paymentmethod_offline_payment_label', $label_name);
            update_option('offline_payment_email', $email);
            update_option('offline_payment_display_name', $display_name);

            $this->db->where('name', 'offline_payment');
            $this->db->update(db_prefix() . 'paymentgateways', [
                'display_name' => $display_name,
            ]);

            set_alert('success', _l('offline_payment_settings_updated'));
            redirect(admin_url('offline_payment/settings'), 'refresh');
        }

        $email = get_option('offline_payment_email');
        if (empty($email)) {
            $email = get_option('companyemail');
        }

        $data['email'] = $email;
        $data['display_name'] = get_option('offline_payment_display_name');
        $data['title'] = _l('offline_payment_settings');

        $this->load->view('settings', $data);
    }

    public function banks()
    {
        $data['banks'] = $this->offline_payment_model->get_banks();

        if ($this->input->post()) {
            $action = $this->input->post('action');

            if ($action === 'add') {
                $bank_name = trim($this->input->post('bank_name'));
                if ($bank_name) {
                    $this->offline_payment_model->add_bank($bank_name);
                    set_alert('success', _l('offline_payment_bank_name') . ' ' . _l('added_successfully'));
                } else {
                    set_alert('warning', _l('offline_payment_bank_name') . ' ' . _l('not_added'));
                }
            }

            if ($action === 'delete') {
                $bank_id = (int)$this->input->post('bank_id');
                if ($this->offline_payment_model->delete_bank($bank_id)) {
                    set_alert('success', _l('deleted_successfully'));
                } else {
                    set_alert('danger', _l('deletion_failed'));
                }
            }

            redirect(admin_url('offline_payment/banks'), 'refresh');
        }

        $data['title'] = _l('offline_payment_manage_banks');
        $this->load->view('bank_list', $data);
    }

    public function revert_payment($id)
    {
        $success = $this->offline_payment_model->revert_payment($id);
        set_alert($success ? 'success' : 'danger', $success ? _l('payment_reverted_successfully') : _l('payment_revert_failed'));
        redirect(admin_url('offline_payment'));
    }

    public function confirm_payment($id)
    {
        $payment = $this->offline_payment_model->get_payment($id);
        if (!$payment) {
            set_alert('danger', _l('payment_confirm_failed'));
            redirect(admin_url('offline_payment'));
        }

        $success = $this->offline_payment_model->confirm_payment($id);

        if ($success) {
            $this->load->model('invoices_model');
            $invoice = $this->invoices_model->get($payment['invoiceid']);
            if ($invoice && $invoice->status != Invoices_model::STATUS_PAID) {
                $this->invoices_model->mark_as_paid($payment['invoiceid']);
            }
            set_alert('success', _l('payment_confirmed_successfully'));
        } else {
            set_alert('danger', _l('payment_confirm_failed'));
        }

        redirect(admin_url('offline_payment'));
    }

    public function delete_bank($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbloffline_payment_banks');
    }

    public function update_note($id)
    {
        if (!is_admin()) {
            access_denied('Offline Payment');
        }

        $note = $this->input->post('admin_notes');
        $updated = $this->db->update('tbl_offline_payments', ['admin_notes' => $note], ['id' => $id]);

        set_alert($updated ? 'success' : 'danger', $updated ? _l('admin_notes') . ' ' . _l('updated_successfully') : _l('update_failed'));
        redirect(admin_url('offline_payment'), 'refresh');
    }

    private function send_payment_email($payment_id)
    {
        $this->load->model('offline_payment_model');
        $payment = $this->offline_payment_model->get_payment($payment_id);

        if (!$payment) {
            log_activity('Offline payment email not sent: payment not found.');
            return false;
        }

        $client_name = get_company_name($payment['clientid']);
        $invoice_id = $payment['invoiceid'];
        $bank = $payment['bank'];
        $reference = $payment['reference'];
        $phone_prefix = $payment['phone_prefix'];
        $phone_number = $payment['phone_number'];
        $date_submitted = $payment['date_submitted'];

        $email_body = $this->load->view('email_template', [
            'client_name' => $client_name,
            'invoice_id' => $invoice_id,
            'bank' => $bank,
            'reference' => $reference,
            'phone_prefix' => $phone_prefix,
            'phone_number' => $phone_number,
            'date_submitted' => $date_submitted,
        ], true);

        $to = get_option('offline_payment_email');
        $subject = 'New Offline Payment Submitted';
        $attachments = [];

        if (!empty($payment['attachment'])) {
            $path = FCPATH . 'uploads/offline_payment_receipts/' . $payment['id'] . '/' . $payment['attachment'];
            if (file_exists($path)) {
                $attachments[] = $path;
            }
        }

        return send_mail($to, $subject, $email_body, '', '', $attachments);
    }

    public function get_payment($id)
    {
        return $this->db->get_where('tbl_offline_payments', ['id' => $id])->row_array();
    }

    public function submit()
    {
        if ($this->input->post()) {
            $data = $this->input->post();

            $payment_id = $this->offline_payment_model->add_payment($data);

            if ($payment_id) {
                $this->send_payment_email($payment_id);
                set_alert('success', _l('offline_payment_sent_successfully'));
            } else {
                set_alert('danger', _l('offline_payment_submission_failed'));
            }

            redirect(site_url('clients/invoices/' . $data['invoiceid']));
        }
    }
}
