<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-6">
        <div class="panel_s">
          <div class="panel-body">
            <h4 class="bold"><?php echo _l('offline_payment_manage_banks'); ?></h4>

            <?php echo form_open(admin_url('offline_payment/banks')); ?>
              <div class="form-group">
                <label for="bank_name"><?php echo _l('offline_payment_bank_name'); ?></label>
                <input type="text" name="bank_name" class="form-control" required>
              </div>
              <input type="hidden" name="action" value="add">
              <button type="submit" class="btn btn-info"><?php echo _l('offline_payment_add_bank'); ?></button>
            <?php echo form_close(); ?>

            <hr />

            <h5><?php echo _l('offline_payment_banks_list'); ?></h5>

            <?php if (empty($banks)): ?>
              <p><?php echo _l('offline_payment_no_banks'); ?></p>
            <?php else: ?>
              <ul class="list-group">
                <?php foreach ($banks as $bank): ?>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?php echo $bank['name']; ?>
                    <?php echo form_open(admin_url('offline_payment/banks'), ['style' => 'display:inline;']); ?>
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="bank_id" value="<?php echo $bank['id']; ?>">
                      <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this bank?');">
                        <i class="fa fa-trash"></i>
                      </button>
                    <?php echo form_close(); ?>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php init_tail(); ?>
