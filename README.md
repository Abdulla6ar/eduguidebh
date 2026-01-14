# 🎓 EduGuide Registration System

Complete professional student registration system with admin dashboard for managing university applications.

## 📋 Table of Contents

- [Features](#features)
- [Technology Stack](#technology-stack)
- [Project Structure](#project-structure)
- [Local Setup](#local-setup)
- [Deployment to DigitalOcean](#deployment-to-digitalocean)
- [Configuration](#configuration)
- [Usage](#usage)
- [Admin Credentials](#admin-credentials)
- [Troubleshooting](#troubleshooting)

---

## ✨ Features

### Student Registration Form
- ✅ Multi-step registration (3 steps)
- ✅ Bilingual support (Arabic/English)
- ✅ File upload functionality for documents
- ✅ Real-time form validation
- ✅ Responsive design (mobile-friendly)
- ✅ Program selection (PhD, Masters, Bachelor)

### Admin Dashboard
- ✅ Secure login system (5 admin users)
- ✅ Real-time statistics (Total, Today, Week, Month)
- ✅ Advanced filtering (by name, date, program)
- ✅ View complete registration details
- ✅ Download/view attached documents
- ✅ Export data to CSV
- ✅ Auto-refresh every 30 seconds
- ✅ Professional email template generator

### Email System
- ✅ One-click email generation
- ✅ Pre-filled student information
- ✅ Professional signature
- ✅ Copy to clipboard functionality
- ✅ Direct email client integration

---

## 🛠 Technology Stack

- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
- **Storage:** localStorage (browser-based)
- **Server:** Nginx (for DigitalOcean deployment)
- **File Format:** Base64 encoding for document storage

---

## 📁 Project Structure

```
eduguide-registration/
├── registration-form.html          # Main registration form
├── admin-login.html                # Admin login page
├── admin-dashboard.html            # Admin dashboard
├── registration-details.html       # Registration details & email generator
├── SYSTEM_GUIDE.md                # Quick start guide
├── README.md                       # This file (deployment guide)
│
├── University Logos (11 files):
├── KU LOGO-2.png
├── University-of-Technology-Bahrain-1.png
├── BUB_Logo-1.png
├── AU Logo-1.png
├── UCB_Logo (1)-1.png
├── strathclyde_bahrain_logo-1.png
├── logo-vatel-1.png
├── 63c7dafe5d20aa1ad494d3dd_BIBFLogo (1)-1.png
├── 63bfe80270936246fad736e1_GU_Logo-1.png
├── 63a327f073bbfdb0d3168a03_شعار جامعة العلوم التطبيقية (1)-1.png
└── 63a327f073bbfd4a07168a1f_RUW logo-1.png
```

---

## 🚀 Local Setup

### Prerequisites
- Any modern web browser (Chrome, Firefox, Safari, Edge)
- Text editor (VS Code, Sublime, etc.) - optional

### Steps

1. **Download all files** to a single folder
2. **Open `registration-form.html`** in your web browser
3. **Test the registration form** by filling it out
4. **Access admin panel:**
   - Click "Admin" link in footer
   - Login: `edu1` / `edubahrain`
5. **View registrations** in the dashboard

---

## 🌐 Deployment to DigitalOcean

### Step 1: Prepare Your Files

1. **Create a deployment folder:**
```bash
mkdir eduguide-registration
cd eduguide-registration
```

2. **Copy all files to this folder:**
   - All HTML files
   - All university logo images
   - README.md and SYSTEM_GUIDE.md

### Step 2: Create DigitalOcean Droplet

1. **Login to DigitalOcean:** https://cloud.digitalocean.com
2. **Create New Droplet:**
   - Choose: **Ubuntu 22.04 LTS**
   - Plan: **Basic** ($6/month recommended)
   - Data Center: Choose closest to Bahrain (Dubai or Frankfurt)
   - Authentication: SSH key or Password
   - Hostname: `eduguide-app` or your preferred name

3. **Note your Droplet IP address** (e.g., 123.45.67.89)

### Step 3: Initial Server Setup

1. **Connect via SSH:**
```bash
ssh root@YOUR_DROPLET_IP
```

2. **Update system:**
```bash
apt update && apt upgrade -y
```

3. **Install Nginx:**
```bash
apt install nginx -y
```

4. **Install certbot for SSL (optional but recommended):**
```bash
apt install certbot python3-certbot-nginx -y
```

### Step 4: Upload Your Files

**Option A: Using SCP (from your local machine):**
```bash
scp -r /Users/ahr/Downloads/*.html root@YOUR_DROPLET_IP:/var/www/html/
scp -r /Users/ahr/Downloads/*.png root@YOUR_DROPLET_IP:/var/www/html/
scp /Users/ahr/Downloads/*.md root@YOUR_DROPLET_IP:/var/www/html/
```

**Option B: Using Git:**
```bash
# On your local machine, initialize git repository
cd /Users/ahr/Downloads
git init
git add *.html *.png *.md
git commit -m "Initial commit - EduGuide Registration System"
git branch -M main
git remote add origin YOUR_GITHUB_REPO_URL
git push -u origin main

# On DigitalOcean droplet
cd /var/www/html
git clone YOUR_GITHUB_REPO_URL .
```

### Step 5: Configure Nginx

1. **Edit Nginx configuration:**
```bash
nano /etc/nginx/sites-available/default
```

2. **Replace contents with:**
```nginx
server {
    listen 80;
    listen [::]:80;
    
    server_name yourdomain.com www.yourdomain.com;
    
    root /var/www/html;
    index registration-form.html;
    
    location / {
        try_files $uri $uri/ =404;
    }
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    
    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

3. **Test Nginx configuration:**
```bash
nginx -t
```

4. **Restart Nginx:**
```bash
systemctl restart nginx
```

### Step 6: Configure Domain DNS

1. **In your domain registrar (Namecheap, GoDaddy, etc.):**
   - Add **A Record**: `@` pointing to `YOUR_DROPLET_IP`
   - Add **A Record**: `www` pointing to `YOUR_DROPLET_IP`

2. **Wait 5-30 minutes for DNS propagation**

### Step 7: Setup SSL Certificate (HTTPS)

1. **Install SSL certificate:**
```bash
certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

2. **Follow prompts:**
   - Enter your email
   - Agree to terms
   - Choose option 2: Redirect HTTP to HTTPS

3. **Test auto-renewal:**
```bash
certbot renew --dry-run
```

### Step 8: File Permissions

```bash
cd /var/www/html
chmod 644 *.html *.png *.md
chown -R www-data:www-data /var/www/html
```

### Step 9: Verify Deployment

1. **Visit your domain:** `https://yourdomain.com`
2. **Test registration form**
3. **Test admin login**
4. **Test file uploads**

---

## ⚙️ Configuration

### Change Admin Passwords

Edit `admin-login.html`, find this section:
```javascript
const validUsers = {
    'edu1': 'edubahrain',
    'edu2': 'edubahrain',
    'edu3': 'edubahrain',
    'edu4': 'edubahrain',
    'edu5': 'edubahrain'
};
```

Change passwords as needed:
```javascript
const validUsers = {
    'edu1': 'your_new_password_1',
    'edu2': 'your_new_password_2',
    'edu3': 'your_new_password_3',
    'edu4': 'your_new_password_4',
    'edu5': 'your_new_password_5'
};
```

### Customize Email Template

Edit `registration-details.html`, find the `generateEmailTemplate()` function and modify the email content.

### Add/Remove Universities

Edit `registration-form.html`, find the footer section and add/remove logo images.

---

## 📖 Usage

### For Students

1. Visit: `https://yourdomain.com`
2. Fill out the registration form (3 steps)
3. Upload required documents
4. Submit application
5. Wait for university contact

### For Admins

1. Scroll to bottom of registration page
2. Click "Admin" link
3. Login with credentials:
   - Username: `edu1` to `edu5`
   - Password: `edubahrain` (or your custom password)
4. View dashboard with all registrations
5. Use filters to search specific applications
6. Click "View" to see details
7. Click "Email" to generate admission email
8. Export to CSV for reports

---

## 🔐 Admin Credentials

**Default Login:**
- Username: `edu1`, `edu2`, `edu3`, `edu4`, `edu5`
- Password: `edubahrain`

**⚠️ IMPORTANT:** Change these passwords before deploying to production!

---

## 📊 Data Management

### Backup Data

**Export to CSV regularly:**
1. Login to admin dashboard
2. Click "Export to CSV" button
3. Save file to your computer

**Browser localStorage backup:**
```javascript
// Open browser console (F12)
let data = localStorage.getItem('eduguide_registrations');
console.log(data);
// Copy and save this data
```

### Clear All Data

```javascript
// Open browser console (F12) on registration form page
localStorage.removeItem('eduguide_registrations');
location.reload();
```

---

## 🐛 Troubleshooting

### Issue: Files won't upload
**Solution:** Check file size. localStorage has ~5-10MB limit. Keep files under 500KB each.

### Issue: Can't access admin dashboard
**Solution:** 
1. Check you're using correct credentials
2. Clear browser cache
3. Try incognito/private browsing mode

### Issue: Data disappeared
**Solution:**
- Data is stored in browser localStorage
- Clearing browser data will delete registrations
- Always export to CSV regularly

### Issue: Website not accessible
**Solution:**
1. Check DNS propagation: https://dnschecker.org
2. Verify Nginx is running: `systemctl status nginx`
3. Check firewall: `ufw allow 'Nginx Full'`
4. Check SSL certificate: `certbot certificates`

### Issue: Images not loading
**Solution:**
1. Verify all image files uploaded to `/var/www/html`
2. Check file permissions: `ls -la /var/www/html/*.png`
3. Check Nginx error log: `tail -f /var/log/nginx/error.log`

---

## 🔄 Updates and Maintenance

### Update Files

```bash
# SSH into droplet
ssh root@YOUR_DROPLET_IP

# Backup current version
cp -r /var/www/html /var/www/html.backup

# Update files (if using git)
cd /var/www/html
git pull

# Or upload new files via SCP
# Then restart Nginx
systemctl restart nginx
```

### Monitor Server

```bash
# Check server status
systemctl status nginx

# View access logs
tail -f /var/log/nginx/access.log

# View error logs
tail -f /var/log/nginx/error.log

# Check disk space
df -h

# Check memory usage
free -h
```

---

## 📞 Support

**EduGuide Educational Support**
- Email: ar@eduguidebh.com
- Website: www.eduguidebh.com
- Office: 13, 644 Street, 406 Sanabis, Bahrain
- CR: 183368

---

## 📝 License

© 2026 EduGuide Educational Support. All rights reserved.

---

## 🚀 Quick Deployment Checklist

- [ ] All HTML files in place
- [ ] All logo images uploaded
- [ ] Nginx installed and configured
- [ ] Domain DNS configured (A records)
- [ ] SSL certificate installed
- [ ] Admin passwords changed
- [ ] Test registration form
- [ ] Test admin login
- [ ] Test file upload/download
- [ ] Test email generation
- [ ] CSV export working
- [ ] Setup regular backups

---

**System Version:** 1.0.0  
**Last Updated:** January 2026  
**Developed by:** Rovo Dev
