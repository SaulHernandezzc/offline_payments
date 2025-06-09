<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-6">
        <div class="panel_s">
          <div class="panel-body">
            <h4 class="bold"><?php echo _l('offline_payment_settings'); ?></h4>

            <?php echo form_open(admin_url('offline_payment/settings')); ?>

              <div class="form-group">
                <label for="offline_payment_email"><?php echo _l('offline_payment_email'); ?></label>
                <input type="email" class="form-control" name="offline_payment_email" value="<?php echo html_escape($email); ?>">
              </div>

              <div class="form-group">
                <label for="offline_payment_display_name"><?php echo _l('offline_payment_display_name'); ?></label>
                <input type="text" class="form-control" name="offline_payment_display_name" value="<?php echo html_escape($display_name); ?>">
              </div>

              <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
              <a href="<?php echo admin_url('offline_payment/banks'); ?>" class="btn btn-default">
                <?php echo _l('offline_payment_manage_banks'); ?>
              </a>

            <?php echo form_close(); ?>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php init_tail(); ?>
