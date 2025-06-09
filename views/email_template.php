<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f5f5f5;padding:20px;font-family:Arial,sans-serif;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="20" cellspacing="0" style="background-color:#ffffff;border:1px solid #ddd;border-radius:6px;">
        <tr>
          <td align="center" style="background-color:#343a40;color:#ffffff;font-size:18px;font-weight:bold;">
            Offline Payment Submitted
          </td>
        </tr>
        <tr>
          <td style="color:#333333;font-size:14px;">
            <p><strong>Client:</strong> <?php echo $client_name; ?></p>
            <p><strong>Invoice:</strong> #<?php echo $invoice_id; ?></p>
            <p><strong>Bank:</strong> <?php echo $bank; ?></p>
            <p><strong>Reference:</strong> <?php echo $reference; ?></p>
            <p><strong>Phone:</strong> +<?php echo $phone_prefix . ' ' . $phone_number; ?></p>
            <p><strong>Date:</strong> <?php echo _dt($date_submitted); ?></p>
          </td>
        </tr>
        <tr>
          <td style="color:#555;font-size:13px;text-align:center;">
            This is an automatic notification from your CRM.
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
