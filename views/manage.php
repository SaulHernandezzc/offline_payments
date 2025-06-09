<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/plugins/datatables/datatables.min.css'); ?>">
<script src="<?php echo base_url('assets/plugins/datatables/datatables.min.js'); ?>"></script>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <h4 class="tw-mb-3"><?php echo _l('offline_payment_requests'); ?></h4>

        <div class="panel_s">
          <div class="panel-body table-responsive">

            <?php if (count($payments) === 0): ?>
              <p><?php echo _l('no_payment_records_found'); ?></p>
            <?php else: ?>

<div class="mb-4">
  <div class="row">
    <div class="col-md-2">
      <div class="panel_s text-center">
        <div class="panel-body">
          <h4><?php echo $dashboard['total']; ?></h4>
          <span><?php echo _l('offline_dashboard_total'); ?></span>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="panel_s text-center">
        <div class="panel-body">
          <h4 class="text-success"><?php echo $dashboard['paid']; ?></h4>
          <span><?php echo _l('offline_dashboard_paid'); ?></span>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="panel_s text-center">
        <div class="panel-body">
          <h4 class="text-danger"><?php echo $dashboard['cancelled']; ?></h4>
          <span><?php echo _l('offline_dashboard_cancelled'); ?></span>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="panel_s text-center">
        <div class="panel-body">
          <h5><?php echo $dashboard['last_payment']; ?></h5>
          <span><?php echo _l('offline_dashboard_last'); ?></span>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="panel_s text-center">
        <div class="panel-body">
          <h4><?php echo $dashboard['recent_7_days']; ?></h4>
          <span><?php echo _l('offline_dashboard_last_7_days'); ?></span>
        </div>
      </div>
    </div>
  </div>

  <?php if (!empty($dashboard['top_banks'])): ?>
    <div class="mt-2">
      <strong><?php echo _l('offline_dashboard_top_banks'); ?>:</strong>
      <ul class="list-inline">
        <?php foreach ($dashboard['top_banks'] as $bank => $count): ?>
          <li class="list-inline-item">
            <span class="label label-default"><?php echo $bank . ': ' . $count; ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>
</div>



           <!-- FILTROS CENTRADOS -->
<div class="mb-4 text-center">
  <form method="get" class="form-inline d-inline-flex justify-content-center flex-wrap gap-2">
    <div class="form-group mr-2 mb-2">
      <label for="status_filter" class="mr-1"><?php echo _l('status'); ?>:</label>
      <select name="status" id="status_filter" class="form-control input-sm">
        <option value=""><?php echo _l('all'); ?></option>
        <option value="paid" <?php echo ($this->input->get('status') === 'paid') ? 'selected' : ''; ?>><?php echo _l('paid'); ?></option>
        <option value="cancelled" <?php echo ($this->input->get('status') === 'cancelled') ? 'selected' : ''; ?>><?php echo _l('cancelled'); ?></option>
      </select>
    </div>
    <div class="form-group mr-2 mb-2">
      <label for="bank_filter" class="mr-1"><?php echo _l('offline_payment_bank_name'); ?>:</label>
      <select name="bank" id="bank_filter" class="form-control input-sm">
        <option value=""><?php echo _l('all'); ?></option>
        <?php foreach ($all_banks as $bank): ?>
          <option value="<?php echo $bank['name']; ?>" <?php echo ($this->input->get('bank') === $bank['name']) ? 'selected' : ''; ?>>
            <?php echo $bank['name']; ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group mb-2">
      <button type="submit" class="btn btn-primary btn-sm"><?php echo _l('filter'); ?></button>
      <a href="<?php echo admin_url('offline_payment'); ?>" class="btn btn-default btn-sm ml-2"><?php echo _l('reset'); ?></a>
    </div>
  </form>
</div>


              <table class="table table-striped" id="offlinePaymentsTable">
                <thead>
                  <tr>
                    <th>#</th>
                    <th><?php echo _l('invoice'); ?></th>
                    <th><?php echo _l('client'); ?></th>
                    <th><?php echo _l('offline_payment_bank_name'); ?></th>
                    <th><?php echo _l('offline_payment_reference'); ?></th>
                    <th><?php echo _l('status'); ?></th>
                    <th><?php echo _l('date'); ?></th>
                    <th><?php echo _l('offline_payment_receipt'); ?></th>
                    <th><?php echo _l('actions'); ?></th>
                    <th><?php echo _l('details'); ?></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($payments as $payment): ?>
                    <tr>
                      <td><?php echo $payment['id']; ?></td>
                      <td>
                        <a href="<?php echo admin_url('invoices/list_invoices/' . $payment['invoiceid']); ?>" target="_blank">
                          #<?php echo $payment['invoiceid']; ?>
                        </a>
                      </td>
                      <td><?php echo get_company_name($payment['clientid']); ?></td>
                      <td><?php echo $payment['bank']; ?></td>
                      <td><?php echo $payment['reference']; ?></td>
                      <td>
                        <span class="label label-<?php echo $payment['status'] === 'paid' ? 'success' : 'danger'; ?>">
                          <?php echo ucfirst($payment['status']); ?>
                        </span>
                      </td>
                      <td><?php echo _dt($payment['date_submitted']); ?></td>
                      <td>
                        <?php if (!empty($payment['attachment'])): ?>
                          <a href="<?php echo base_url('uploads/offline_payment_receipts/' . $payment['id'] . '/' . $payment['attachment']); ?>" target="_blank" class="btn btn-default btn-sm">
                            <?php echo _l('offline_payment_view_receipt'); ?>
                          </a>
                        <?php else: ?>
                          <span class="text-muted"><?php echo _l('no_upload_found'); ?></span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if ($payment['status'] === 'paid'): ?>
                          <a href="<?php echo admin_url('offline_payment/revert_payment/' . $payment['id']); ?>" class="btn btn-sm revert-btn">
                            <?php echo _l('offline_payment_revert'); ?>
                          </a>
                        <?php elseif ($payment['status'] === 'cancelled'): ?>
                          <a href="<?php echo admin_url('offline_payment/confirm_payment/' . $payment['id']); ?>" class="btn btn-sm confirm-btn" onclick="return confirm('<?php echo _l('offline_payment_confirm'); ?>?');">
                              <?php echo _l('offline_payment_confirm'); ?>
                            </a>
                        <?php endif; ?>
                      </td>
                      <td>
                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#details_<?php echo $payment['id']; ?>">
                          <?php echo _l('view'); ?>
                        </button>

                        <!-- Modal de detalles con edición de nota -->
                        <div class="modal fade" id="details_<?php echo $payment['id']; ?>" tabindex="-1" role="dialog">
                          <div class="modal-dialog modal-sm" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title"><?php echo _l('offline_payment'); ?> #<?php echo $payment['id']; ?></h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                              </div>
                              <form method="post" action="<?php echo admin_url('offline_payment/update_note/' . $payment['id']); ?>">
  <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
 
                                <div class="modal-body">
                                  <p><strong><?php echo _l('offline_payment_phone_prefix'); ?>:</strong> <?php echo $payment['phone_prefix']; ?></p>
                                  <p><strong><?php echo _l('offline_payment_phone_number'); ?>:</strong> <?php echo $payment['phone_number']; ?></p>
                                  <p><strong><?php echo _l('offline_payment_document_type'); ?>:</strong> <?php echo $payment['document_type']; ?></p>
                                  <p><strong><?php echo _l('offline_payment_reference'); ?>:</strong> <?php echo $payment['reference']; ?></p>
                                  <hr>
                                  <div class="form-group">
                                    <label for="admin_notes"><?php echo _l('admin_notes'); ?></label>
                                    <textarea name="admin_notes" rows="4" class="form-control"><?php echo html_escape($payment['admin_notes']); ?></textarea>
                                  </div>
                                </div>
                                <div class="modal-footer">
    <button type="submit" class="btn btn-primary"><?php echo _l('save'); ?></button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _l('close'); ?></button>
  </div>
                              </form>
                            </div>
                          </div>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php endif; ?>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php init_tail(); ?>

<script src="<?php echo base_url('assets/plugins/datatables/datatables.min.js'); ?>"></script>

<script>
$(function(){
  console.log("DataTable exists:", typeof $.fn.DataTable);

  $('#offlinePaymentsTable').DataTable({
    pageLength: 15,
    order: [[0, 'desc']],
    language: {
      url: "<?php echo base_url('assets/plugins/datatables/locales/english.json'); ?>"
    }
  });
});
</script>

<style>
table.dataTable thead .sorting:after,
table.dataTable thead .sorting_asc:after,
table.dataTable thead .sorting_desc:after {
  font-family: Arial, sans-serif;
  content: '';
  position: absolute;
  right: 0.75em;
  color: #888;
  font-size: 12px;
  opacity: 0.6;
}

table.dataTable thead .sorting_asc:after {
  content: '↑';
}

table.dataTable thead .sorting_desc:after {
  content: '↓';
}
.revert-btn {
  color: #dc3545 !important;
  border: 1px solid #dc3545 !important;
  background-color: transparent !important;
  padding: 5px 10px;
  border-radius: 3px;
  font-size: 13px;
}
.revert-btn:hover {
  background-color: #dc3545 !important;
  color: white !important;
}
.confirm-btn {
  color: #28a745 !important;
  border: 1px solid #28a745 !important;
  background-color: transparent !important;
  padding: 5px 10px;
  border-radius: 3px;
  font-size: 13px;
}
.confirm-btn:hover {
  background-color: #28a745 !important;
  color: white !important;
}

</style>

