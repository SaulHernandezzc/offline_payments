<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="form-group">
  <label for="settings[active]">Active</label><br>
  <div class="radio radio-primary radio-inline">
    <input type="radio" name="settings[active]" value="1" <?php if (get_option('paymentmethod_offline_payment_active') == '1') echo 'checked'; ?>>
    <label><?php echo _l('settings_yes'); ?></label>
  </div>
  <div class="radio radio-primary radio-inline">
    <input type="radio" name="settings[active]" value="0" <?php if (get_option('paymentmethod_offline_payment_active') == '0') echo 'checked'; ?>>
    <label><?php echo _l('settings_no'); ?></label>
  </div>
</div>
<div class="form-group">
  <label for="paymentmethod_offline_payment_label"><?php echo _l('offline_payment_display_name'); ?></label>
  <input type="text" class="form-control" name="paymentmethod_offline_payment_label"
         value="<?php echo html_escape(get_option('paymentmethod_offline_payment_label')); ?>">
</div>
