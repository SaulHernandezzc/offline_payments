<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Offline_payment_model extends App_Model
{
    protected $table = 'tbl_offline_payments';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_banks()
    {
        return $this->db->get('tbloffline_payment_banks')->result_array();
    }

    public function add_bank($name)
    {
        return $this->db->insert('tbloffline_payment_banks', ['name' => $name]);
    }

    public function delete_bank($id)
    {
        return $this->db->delete('tbloffline_payment_banks', ['id' => $id]);
    }

    public function add_payment($data)
    {
        $success = $this->db->insert($this->table, $data);
        if ($success) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function send_admin_notification($payment_id, $invoice_id)
    {
        $email = get_option('offline_payment_email');
        if (!$email) {
            return;
        }

        $this->load->model('invoices_model');
        $invoice = $this->invoices_model->get($invoice_id);
        $link = admin_url('offline_payment');

        $message = 'New offline payment submitted for invoice #' . $invoice->number . '.<br>';
        $message .= 'View it here: <a href="' . $link . '">' . $link . '</a>';

        send_mail_template([
            'sent_to'   => $email,
            'subject'   => 'New Offline Payment Submitted',
            'message'   => $message,
            'fromname'  => get_option('companyname'),
        ]);
    }

    public function revert_payment($id)
    {
        return $this->db->update($this->table, ['status' => 'cancelled'], ['id' => $id]);
    }

    public function confirm_payment($id)
    {
        return $this->db->update($this->table, ['status' => 'paid'], ['id' => $id]);
    }

    public function get_payment($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row_array();
    }

    public function get_all_payments($filters = [])
    {
        if (!empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }

        if (!empty($filters['bank'])) {
            $this->db->where('bank', $filters['bank']);
        }

        return $this->db->order_by('date_submitted', 'DESC')->get($this->table)->result_array();
    }

    public function get_dashboard_metrics($filters = [])
    {
        if (!empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }

        if (!empty($filters['bank'])) {
            $this->db->where('bank', $filters['bank']);
        }

        $query = $this->db->get($this->table)->result();

        $total = count($query);
        $paid = 0;
        $cancelled = 0;
        $last_payment = '';
        $recent_7_days = 0;
        $banks_count = [];

        foreach ($query as $row) {
            if ($row->status == 'paid') {
                $paid++;
                $banks_count[$row->bank] = ($banks_count[$row->bank] ?? 0) + 1;
            }
            if ($row->status == 'cancelled') {
                $cancelled++;
            }
            if ($last_payment === '' || strtotime($row->date_submitted) > strtotime($last_payment)) {
                $last_payment = $row->date_submitted;
            }
            if (strtotime($row->date_submitted) > strtotime('-7 days')) {
                $recent_7_days++;
            }
        }

        arsort($banks_count);
        $top_banks = array_slice($banks_count, 0, 3, true);

        return [
            'total'          => $total,
            'paid'           => $paid,
            'cancelled'      => $cancelled,
            'last_payment'   => $last_payment ? _dt($last_payment) : 'N/A',
            'recent_7_days'  => $recent_7_days,
            'top_banks'      => $top_banks,
        ];
    }
}
