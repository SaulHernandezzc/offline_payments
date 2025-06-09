<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<h4 class="bold mbot15">
  <i class="fa fa-credit-card"></i> 
  <?php echo get_option('offline_payment_display_name') ?: _l('offline_payment'); ?>
</h4>

<p class="text-muted">
  <?php echo _l('offline_payment_method_description'); ?>
</p>

<?php echo form_open_multipart($this->uri->uri_string(), ['id' => 'offline-payment-form']); ?>
<input type="hidden" name="paymentmethod" value="offline_payment">

<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label for="bank"><?php echo _l('offline_payment_select_bank'); ?></label>
      <select name="bank" class="form-control" required>
        <option value=""><?php echo _l('offline_payment_select_bank'); ?></option>
        <?php if (!empty($banks)) : ?>
          <?php foreach ($banks as $bank): ?>
            <option value="<?php echo html_escape($bank['name']); ?>">
              <?php echo html_escape($bank['name']); ?>
            </option>
          <?php endforeach; ?>
        <?php endif; ?>
      </select>
    </div>
  </div>

  <div class="col-md-6">
    <div class="form-group">
      <label for="document_type"><?php echo _l('offline_payment_document_type'); ?></label>
      <select name="document_type" class="form-control" required>
        <option value="ID">ID</option>
        <option value="Passport">Passport</option>
        <option value="Driver License">Driver License</option>
      </select>
    </div>
  </div>

  <div class="col-md-4">
    <div class="form-group">
      <label for="phone_prefix"><?php echo _l('offline_payment_phone_prefix'); ?></label>
      <select name="phone_prefix" class="form-control" required>
        <option value="+1">+1</option>
        <option value="+34">+34</option>
        <option value="+57">+57</option>
        <option value="+44">+44</option>
      </select>
    </div>
  </div>

  <div class="col-md-8">
    <div class="form-group">
      <label for="phone_number"><?php echo _l('offline_payment_phone_number'); ?></label>
      <input type="text" name="phone_number" class="form-control" required>
    </div>
  </div>

  <div class="col-md-6">
    <div class="form-group">
      <label for="reference"><?php echo _l('offline_payment_reference'); ?></label>
      <input type="text" name="reference" class="form-control" required>
    </div>
  </div>

  <div class="col-md-6">
    <div class="form-group">
      <label for="attachment"><?php echo _l('offline_payment_upload_receipt'); ?></label>
      <input type="file" name="attachment" class="form-control" accept="image/*" required>
      <small class="text-muted"><?php echo _l('allowed_file_types'); ?>: JPG, PNG, GIF</small>
    </div>
  </div>
</div>

<div class="mtop20">
  <button type="submit" class="btn btn-primary btn-block">
    <i class="fa fa-paper-plane"></i> <?php echo _l('offline_payment_submit'); ?>
  </button>
</div>

<?php echo form_close(); ?>
