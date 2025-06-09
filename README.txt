OFFLINE PAYMENT MODULE FOR PERFEX CRM
=================================================

Version: 1.0
Author: GPS Tracker International
Website: https://trackerinternationalinc.com

DESCRIPTION
-----------
Allows clients to submit manual (offline) payment information for invoices, including:
- Bank selection
- Document type and number
- Phone prefix and number
- Reference field
- Receipt image upload
- Admin panel for managing payments and banks
- Email notification to admin
- Option to revert payments manually

INSTALLATION
------------
1. Upload the entire 'offline_payment' folder into: /modules/
2. Go to Perfex CRM Admin Panel > Setup > Modules.
3. Click "Activate" on the "Offline Payment" module.

USAGE
-----
1. In Admin Panel:
   - Go to 'Offline Payment' in the sidebar to see submitted payments.
   - Configure email notifications and payment method display name in 'Settings'.
   - Manage available banks in 'Manage Banks'.

2. In Client Area:
   - Clients can select "Offline Payment" as a method when paying an invoice.
   - A form will appear to enter their payment data and upload a receipt.

3. Admin receives an email notification, and the invoice is automatically marked as "Paid".
   - Admin can later revert the payment manually from the panel.

FILES CREATED
-------------
- Tables: 
  - tbloffline_payment_requests
  - tbloffline_payment_banks

- Options:
  - offline_payment_email
  - offline_payment_display_name

UNINSTALLATION
--------------
When uninstalling the module, the created tables and options will be removed automatically.

SUPPORT
-------
For questions or customization help, contact the developer:
https://trackerinternationalinc.com
