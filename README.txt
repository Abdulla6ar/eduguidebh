====================================
EDUGUIDEBH - READY TO UPLOAD
====================================

Server: sftp://164.90.228.133
Username: root
Port: 22

UPLOAD INSTRUCTIONS:
====================

1. Connect FileZilla to server
2. Navigate to: /var/www/html/

3. Upload these files:
   - registration-form.html → /var/www/html/
   - dashboard.html → /var/www/html/
   - login.html → /var/www/html/

4. Create folder: /var/www/html/api/
5. Upload api folder contents:
   - submit-registration.php → /var/www/html/api/
   - get-registrations.php → /var/www/html/api/

6. Create folder: /var/www/html/uploads/
   Set permissions: 775

7. IMPORTANT: Edit both PHP files and update:
   'username' => 'YOUR_DB_USERNAME'
   'password' => 'YOUR_DB_PASSWORD'

DONE!

Test at: http://164.90.228.133/registration-form.html
