# 🚀 EduGuide Registration System - Deployment Checklist

## Pre-Deployment Preparation

### 1. Local Testing ✓
- [ ] Test registration form submission
- [ ] Test all 3 steps navigation
- [ ] Test file uploads (all types: PDF, JPG, PNG)
- [ ] Test admin login with all 5 users
- [ ] Test dashboard statistics
- [ ] Test filtering (name, date, program)
- [ ] Test registration details view
- [ ] Test email generation and copy
- [ ] Test file download/view functionality
- [ ] Test CSV export
- [ ] Test on multiple browsers (Chrome, Firefox, Safari)
- [ ] Test on mobile devices

### 2. Security Configuration ✓
- [ ] Change default admin passwords
- [ ] Review and update email signature
- [ ] Remove any test data from localStorage
- [ ] Check all file paths are relative (not absolute)

### 3. Files Preparation ✓
- [ ] All HTML files present
- [ ] All 11 university logo images present
- [ ] README.md included
- [ ] SYSTEM_GUIDE.md included
- [ ] .gitignore created

---

## GitHub Setup

### 1. Create GitHub Repository
```bash
# Navigate to your project directory
cd /Users/ahr/Downloads

# Initialize git repository
git init

# Add all files
git add registration-form.html
git add admin-login.html
git add admin-dashboard.html
git add registration-details.html
git add *.png
git add *.md
git add .gitignore

# Create initial commit
git commit -m "Initial commit: EduGuide Registration System v1.0.0"

# Create main branch
git branch -M main

# Add remote repository (replace with your GitHub repo URL)
git remote add origin https://github.com/YOUR_USERNAME/eduguide-registration.git

# Push to GitHub
git push -u origin main
```

### 2. Verify GitHub Repository
- [ ] Visit your GitHub repository URL
- [ ] Check all files are uploaded
- [ ] Verify images are displaying in README
- [ ] Test clone command: `git clone YOUR_REPO_URL`

---

## DigitalOcean Setup

### 1. Create Droplet
- [ ] Login to DigitalOcean
- [ ] Create New Droplet
  - [ ] OS: Ubuntu 22.04 LTS
  - [ ] Plan: Basic $6/month
  - [ ] Data Center: Dubai or Frankfurt
  - [ ] Authentication: SSH key or password
  - [ ] Hostname: eduguide-app
- [ ] Note Droplet IP address: `_________________`

### 2. Initial Server Configuration
```bash
# SSH into your droplet
ssh root@YOUR_DROPLET_IP

# Update system
apt update && apt upgrade -y

# Install Nginx
apt install nginx -y

# Install Certbot for SSL
apt install certbot python3-certbot-nginx -y

# Enable firewall
ufw allow 'Nginx Full'
ufw allow OpenSSH
ufw enable
```

### 3. Upload Files to Server

**Option A: Via Git (Recommended)**
```bash
# On server
cd /var/www/html
rm index.nginx-debian.html  # Remove default page
git clone https://github.com/YOUR_USERNAME/eduguide-registration.git .
```

**Option B: Via SCP**
```bash
# From your local machine
scp /Users/ahr/Downloads/*.html root@YOUR_DROPLET_IP:/var/www/html/
scp /Users/ahr/Downloads/*.png root@YOUR_DROPLET_IP:/var/www/html/
scp /Users/ahr/Downloads/*.md root@YOUR_DROPLET_IP:/var/www/html/
```

### 4. Set File Permissions
```bash
# On server
cd /var/www/html
chmod 644 *.html *.png *.md
chown -R www-data:www-data /var/www/html
```

### 5. Configure Nginx
```bash
# Backup default config
cp /etc/nginx/sites-available/default /etc/nginx/sites-available/default.backup

# Edit config
nano /etc/nginx/sites-available/default
```

**Paste this configuration:**
```nginx
server {
    listen 80;
    listen [::]:80;
    
    server_name YOUR_DOMAIN.com www.YOUR_DOMAIN.com;
    
    root /var/www/html;
    index registration-form.html;
    
    location / {
        try_files $uri $uri/ =404;
    }
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    
    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
    
    # Disable access to hidden files
    location ~ /\. {
        deny all;
    }
}
```

```bash
# Test configuration
nginx -t

# Restart Nginx
systemctl restart nginx
systemctl enable nginx
```

### 6. Verify Files on Server
- [ ] Check all HTML files: `ls -la /var/www/html/*.html`
- [ ] Check all images: `ls -la /var/www/html/*.png`
- [ ] Test Nginx: `systemctl status nginx`

---

## Domain Configuration

### 1. DNS Setup
- [ ] Login to your domain registrar (Namecheap, GoDaddy, etc.)
- [ ] Go to DNS Management
- [ ] Add A Record:
  - Host: `@`
  - Value: `YOUR_DROPLET_IP`
  - TTL: 300 (or automatic)
- [ ] Add A Record:
  - Host: `www`
  - Value: `YOUR_DROPLET_IP`
  - TTL: 300 (or automatic)
- [ ] Save changes
- [ ] Wait 5-30 minutes for DNS propagation

### 2. Verify DNS
```bash
# From your local machine
dig YOUR_DOMAIN.com
dig www.YOUR_DOMAIN.com

# Or use online tool: https://dnschecker.org
```

- [ ] DNS pointing to correct IP address
- [ ] Both @ and www records working

---

## SSL Certificate Setup

### 1. Install SSL Certificate
```bash
# On server
certbot --nginx -d YOUR_DOMAIN.com -d www.YOUR_DOMAIN.com
```

Follow prompts:
- [ ] Enter email address
- [ ] Agree to Terms of Service
- [ ] Choose option 2: Redirect HTTP to HTTPS

### 2. Test SSL Certificate
- [ ] Visit: `https://YOUR_DOMAIN.com`
- [ ] Check padlock icon in browser
- [ ] Verify certificate details
- [ ] Test auto-renewal: `certbot renew --dry-run`

### 3. Configure Auto-Renewal
```bash
# Check renewal timer
systemctl status certbot.timer

# If not enabled
systemctl enable certbot.timer
systemctl start certbot.timer
```

---

## Final Testing

### 1. Website Access
- [ ] Visit `https://YOUR_DOMAIN.com`
- [ ] Verify main page loads
- [ ] Check all university logos display correctly
- [ ] Test responsive design (resize browser)

### 2. Registration Form Testing
- [ ] Fill out Step 1 (Personal Info)
- [ ] Fill out Step 2 (Program Selection)
- [ ] Upload files in Step 3
- [ ] Submit form
- [ ] Verify success message

### 3. Admin Dashboard Testing
- [ ] Click "Admin" link in footer
- [ ] Login with: `edu1` / `edubahrain`
- [ ] Verify statistics display
- [ ] Check registration appears in dashboard
- [ ] Test all 5 admin users login

### 4. Registration Details Testing
- [ ] Click "View" on a registration
- [ ] Verify all details display correctly
- [ ] Test "View" button on uploaded files
- [ ] Test "Download" button on uploaded files
- [ ] Click "Copy Email" button
- [ ] Paste email content - verify it's correct

### 5. Export Functionality
- [ ] Click "Export to CSV" in dashboard
- [ ] Verify CSV file downloads
- [ ] Open CSV in Excel
- [ ] Check all data is present

### 6. Cross-Browser Testing
- [ ] Test on Chrome
- [ ] Test on Firefox
- [ ] Test on Safari
- [ ] Test on Edge

### 7. Mobile Device Testing
- [ ] Test on iPhone/iOS
- [ ] Test on Android
- [ ] Test form submission on mobile
- [ ] Test admin dashboard on mobile

---

## Post-Deployment

### 1. Security Hardening
```bash
# On server - Install fail2ban
apt install fail2ban -y
systemctl enable fail2ban
systemctl start fail2ban

# Configure SSH (optional but recommended)
nano /etc/ssh/sshd_config
# Set: PermitRootLogin no
# Set: PasswordAuthentication no (if using SSH keys)
systemctl restart sshd
```

### 2. Setup Monitoring
- [ ] Create DigitalOcean monitoring dashboard
- [ ] Setup uptime monitoring (UptimeRobot or similar)
- [ ] Configure email alerts for downtime

### 3. Backup Strategy
- [ ] Enable DigitalOcean automatic backups ($1.20/month)
- [ ] Document manual backup procedure
- [ ] Schedule regular CSV exports
- [ ] Store backup credentials securely

### 4. Documentation
- [ ] Share admin credentials with team (securely)
- [ ] Document support procedures
- [ ] Create user guide for students
- [ ] Create admin training guide

---

## Maintenance Schedule

### Daily
- [ ] Check dashboard for new registrations
- [ ] Export CSV backup (if new registrations)

### Weekly
- [ ] Review server logs: `tail -100 /var/log/nginx/access.log`
- [ ] Check disk space: `df -h`
- [ ] Verify SSL certificate status

### Monthly
- [ ] Update system packages: `apt update && apt upgrade -y`
- [ ] Review and archive old registrations
- [ ] Check backup integrity
- [ ] Test disaster recovery procedure

### Quarterly
- [ ] Security audit
- [ ] Performance review
- [ ] Update university logos if needed
- [ ] Review and update email templates

---

## Emergency Contacts

**Technical Support:**
- DigitalOcean Support: https://cloud.digitalocean.com/support
- GitHub Support: https://support.github.com

**EduGuide Educational Support:**
- Email: ar@eduguidebh.com
- Website: www.eduguidebh.com
- Office: 13, 644 Street, 406 Sanabis, Bahrain

---

## Rollback Procedure

If something goes wrong:

```bash
# On server
cd /var/www/html

# Restore from backup
cp -r /var/www/html.backup/* /var/www/html/

# Or restore from Git
git reset --hard HEAD
git pull origin main

# Restart Nginx
systemctl restart nginx
```

---

## Success Criteria

- [✓] Website accessible via HTTPS
- [✓] All pages loading correctly
- [✓] Registration form working
- [✓] File uploads functional
- [✓] Admin login successful
- [✓] Dashboard displaying data
- [✓] Email generation working
- [✓] CSV export functional
- [✓] Mobile responsive
- [✓] SSL certificate valid
- [✓] Backups configured

---

**Deployment Date:** _______________  
**Deployed By:** _______________  
**Domain:** _______________  
**Server IP:** _______________  
**GitHub Repo:** _______________

---

🎉 **Deployment Complete!**
