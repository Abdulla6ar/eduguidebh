# 🌊 DigitalOcean Deployment Guide

Complete step-by-step guide to deploy EduGuide Registration System on DigitalOcean.

---

## 📋 Table of Contents

1. [Prerequisites](#prerequisites)
2. [Create DigitalOcean Account](#create-digitalocean-account)
3. [Create Droplet](#create-droplet)
4. [Initial Server Setup](#initial-server-setup)
5. [Install Required Software](#install-required-software)
6. [Upload Your Files](#upload-your-files)
7. [Configure Nginx](#configure-nginx)
8. [Setup Domain DNS](#setup-domain-dns)
9. [Install SSL Certificate](#install-ssl-certificate)
10. [Final Testing](#final-testing)
11. [Troubleshooting](#troubleshooting)

---

## 📌 Prerequisites

Before starting, make sure you have:

- [ ] DigitalOcean account (or create one at https://digitalocean.com)
- [ ] Domain name (e.g., yourdomain.com)
- [ ] Access to domain DNS settings
- [ ] All project files ready (HTML, images, docs)
- [ ] Files pushed to GitHub (optional but recommended)
- [ ] Credit card or PayPal for DigitalOcean billing

**Estimated Cost:** $6-12/month for Basic Droplet + $1.20/month for backups

---

## 🎯 Create DigitalOcean Account

### Step 1: Sign Up

1. Go to: https://cloud.digitalocean.com/registrations/new
2. Sign up with:
   - Email and password, or
   - Google account, or
   - GitHub account
3. Verify your email address
4. Add payment method (credit card or PayPal)

### Step 2: Get $200 Credit (Optional)

Use a referral link to get **$200 free credit** for 60 days:
- Search for "DigitalOcean promo code" or "DigitalOcean referral"
- This gives you plenty of time to test before paying

---

## 💧 Create Droplet

### Step 1: Start Droplet Creation

1. Login to DigitalOcean: https://cloud.digitalocean.com
2. Click **"Create"** (top right) → **"Droplets"**

### Step 2: Choose Image

**Select:** Ubuntu 22.04 (LTS) x64

✅ Why Ubuntu 22.04?
- Long Term Support (LTS)
- Very stable and secure
- Widely documented
- Regular security updates

### Step 3: Choose Plan

**Select:** Basic

**Recommended Options:**

| Plan | CPU | Memory | Storage | Transfer | Price |
|------|-----|--------|---------|----------|-------|
| **Recommended** | 1 CPU | 1 GB | 25 GB | 1000 GB | $6/mo |
| Good for growth | 1 CPU | 2 GB | 50 GB | 2000 GB | $12/mo |

✅ **$6/month plan is sufficient** for starting

### Step 4: Choose Datacenter Region

**Recommended Regions (closest to Bahrain):**

1. **Dubai (AE)** - Best for Middle East (if available)
2. **Frankfurt (FRA1)** - Good for Europe/Middle East
3. **London (LON1)** - Alternative for Europe
4. **Singapore (SGP1)** - Good for Asia

**Select:** Choose the closest region to your users

### Step 5: Authentication

**Option A: SSH Key (Recommended)** 🔒

If you have SSH key:
1. Click "New SSH Key"
2. Paste your public key content
3. Give it a name: "My Computer"

To get your SSH key:
```bash
# On Mac/Linux
cat ~/.ssh/id_rsa.pub
# If no key exists, create one:
ssh-keygen -t rsa -b 4096 -C "your_email@example.com"
```

**Option B: Password** 🔑

If no SSH key:
1. Select "Password"
2. DigitalOcean will email you a root password

### Step 6: Additional Options

**Enable:**
- ✅ **Monitoring** (Free) - Track CPU, disk, bandwidth
- ✅ **IPv6** (Free) - Future-proof

**Optional:**
- Backups (+20% cost = $1.20/month) - **Highly Recommended!**

### Step 7: Finalize

1. **Hostname:** `eduguide-app` (or your preferred name)
2. **Tags:** Add tags like `production`, `eduguide`, `web`
3. **Project:** Default or create "EduGuide"
4. Click **"Create Droplet"**

⏱️ Wait 1-2 minutes for droplet creation

### Step 8: Note Your Droplet IP

Once created, you'll see:
- **Droplet Name:** eduguide-app
- **IP Address:** `123.45.67.89` ← **Copy this!**

**Write it down:**
```
Droplet IP: ____________________
```

---

## 🔧 Initial Server Setup

### Step 1: Connect via SSH

**Using Terminal (Mac/Linux):**
```bash
ssh root@YOUR_DROPLET_IP
```

**Using PuTTY (Windows):**
1. Download PuTTY: https://putty.org
2. Enter IP address
3. Click "Open"
4. Login as: `root`

**First Connection:**
- You'll see a warning about authenticity
- Type `yes` and press Enter
- Enter password (if using password auth)

### Step 2: Change Root Password

If you received a temporary password via email:

```bash
passwd
# Enter new password twice
```

**Use a strong password!** Example format: `EduGuide2026!Secure#`

### Step 3: Update System

```bash
# Update package list
apt update

# Upgrade all packages
apt upgrade -y

# This may take 5-10 minutes
```

### Step 4: Create Sudo User (Recommended)

For better security, create a non-root user:

```bash
# Create new user
adduser eduadmin
# Enter password and details

# Add to sudo group
usermod -aG sudo eduadmin

# Test sudo access
su - eduadmin
sudo ls /root
# Enter password when prompted
```

From now on, you can use `eduadmin` instead of `root`.

---

## 📦 Install Required Software

### Step 1: Install Nginx

```bash
# Install Nginx web server
sudo apt install nginx -y

# Start Nginx
sudo systemctl start nginx

# Enable Nginx to start on boot
sudo systemctl enable nginx

# Check status
sudo systemctl status nginx
# Press 'q' to exit
```

✅ Nginx is now running!

**Test:** Visit `http://YOUR_DROPLET_IP` in browser
- You should see "Welcome to nginx!" page

### Step 2: Install Certbot (for SSL)

```bash
# Install Certbot and Nginx plugin
sudo apt install certbot python3-certbot-nginx -y

# Verify installation
certbot --version
```

### Step 3: Install Git (if using GitHub)

```bash
# Install Git
sudo apt install git -y

# Verify installation
git --version
```

### Step 4: Configure Firewall

```bash
# Allow Nginx
sudo ufw allow 'Nginx Full'

# Allow SSH (important!)
sudo ufw allow OpenSSH

# Enable firewall
sudo ufw enable
# Type 'y' and press Enter

# Check status
sudo ufw status
```

You should see:
```
Status: active

To                         Action      From
--                         ------      ----
Nginx Full                 ALLOW       Anywhere
OpenSSH                    ALLOW       Anywhere
```

---

## 📤 Upload Your Files

### Method A: Using Git (Recommended) ⭐

```bash
# Navigate to web directory
cd /var/www/html

# Remove default page
sudo rm index.nginx-debian.html

# Clone your repository
sudo git clone https://github.com/YOUR_USERNAME/eduguide-registration.git .

# List files to verify
ls -la
```

You should see all your HTML files and images.

### Method B: Using SCP (From Your Computer)

**On your local machine (Mac/Linux):**

```bash
# Navigate to your files
cd /Users/ahr/Downloads

# Upload HTML files
scp *.html root@YOUR_DROPLET_IP:/var/www/html/

# Upload PNG images
scp *.png root@YOUR_DROPLET_IP:/var/www/html/

# Upload documentation
scp *.md root@YOUR_DROPLET_IP:/var/www/html/
```

**On Windows:**

Use WinSCP: https://winscp.net
1. Install WinSCP
2. Connect to your droplet IP
3. Username: `root`
4. Password: Your password
5. Drag and drop files to `/var/www/html`

### Method C: Using SFTP

```bash
# Connect via SFTP
sftp root@YOUR_DROPLET_IP

# Navigate to web directory
cd /var/www/html

# Upload files
put /Users/ahr/Downloads/registration-form.html
put /Users/ahr/Downloads/admin-login.html
put /Users/ahr/Downloads/admin-dashboard.html
put /Users/ahr/Downloads/registration-details.html
put /Users/ahr/Downloads/*.png

# Exit
exit
```

### Step: Verify Files

```bash
# On server
cd /var/www/html
ls -la

# You should see:
# - 4 HTML files
# - 11 PNG image files
# - MD documentation files
```

### Set Correct Permissions

```bash
# Set file permissions
sudo chmod 644 /var/www/html/*.html
sudo chmod 644 /var/www/html/*.png
sudo chmod 644 /var/www/html/*.md

# Set directory permissions
sudo chmod 755 /var/www/html

# Set ownership to Nginx user
sudo chown -R www-data:www-data /var/www/html
```

---

## ⚙️ Configure Nginx

### Step 1: Backup Default Config

```bash
sudo cp /etc/nginx/sites-available/default /etc/nginx/sites-available/default.backup
```

### Step 2: Edit Nginx Configuration

```bash
sudo nano /etc/nginx/sites-available/default
```

### Step 3: Replace with This Configuration

**Delete everything** and paste this:

```nginx
server {
    listen 80;
    listen [::]:80;
    
    # Replace with your actual domain
    server_name yourdomain.com www.yourdomain.com;
    
    root /var/www/html;
    index registration-form.html index.html;
    
    # Main location
    location / {
        try_files $uri $uri/ =404;
    }
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    
    # Cache static assets (images, CSS, JS)
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }
    
    # Deny access to hidden files
    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }
    
    # Deny access to backup files
    location ~ ~$ {
        deny all;
        access_log off;
        log_not_found off;
    }
    
    # Logging
    access_log /var/log/nginx/eduguide_access.log;
    error_log /var/log/nginx/eduguide_error.log;
}
```

**Important:** Replace `yourdomain.com` with your actual domain!

**Save and exit:**
- Press `Ctrl + X`
- Press `Y` (yes)
- Press `Enter` (confirm)

### Step 4: Test Configuration

```bash
sudo nginx -t
```

✅ You should see:
```
nginx: the configuration file /etc/nginx/nginx.conf syntax is ok
nginx: configuration file /etc/nginx/nginx.conf test is successful
```

❌ If you see errors, re-edit the file and fix them.

### Step 5: Restart Nginx

```bash
sudo systemctl restart nginx

# Check status
sudo systemctl status nginx
```

✅ Status should show: **active (running)**

---

## 🌐 Setup Domain DNS

### Step 1: Login to Domain Registrar

Common registrars:
- **Namecheap:** https://namecheap.com
- **GoDaddy:** https://godaddy.com
- **Google Domains:** https://domains.google
- **Cloudflare:** https://cloudflare.com

### Step 2: Access DNS Management

Usually found under:
- "DNS Management"
- "Manage DNS"
- "DNS Settings"
- "Advanced DNS"

### Step 3: Add A Records

**Add Record #1:**
```
Type: A Record
Host: @
Value: YOUR_DROPLET_IP
TTL: Automatic (or 300)
```

**Add Record #2:**
```
Type: A Record
Host: www
Value: YOUR_DROPLET_IP
TTL: Automatic (or 300)
```

**Example:**
```
@ → 123.45.67.89
www → 123.45.67.89
```

### Step 4: Save Changes

Click "Save" or "Add Record"

### Step 5: Wait for DNS Propagation

- **Minimum:** 5-15 minutes
- **Maximum:** 24-48 hours (rare)
- **Average:** 30 minutes

### Step 6: Check DNS Propagation

**Option A: Using dig command**
```bash
dig yourdomain.com
dig www.yourdomain.com
```

Look for your droplet IP in the ANSWER section.

**Option B: Online Tool**

Visit: https://dnschecker.org
- Enter your domain
- Select "A" record
- Check multiple locations

✅ When your IP shows up globally, DNS is propagated!

### Step 7: Test Domain Access

Visit: `http://yourdomain.com`

✅ You should see your registration form!

---

## 🔒 Install SSL Certificate

SSL certificate enables HTTPS (the padlock icon).

### Step 1: Obtain SSL Certificate

```bash
# Replace with your actual domain
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

### Step 2: Follow Prompts

**Prompt 1:** Enter email address
```
Enter email address: your-email@example.com
```

**Prompt 2:** Terms of Service
```
Please read the Terms of Service...
(A)gree/(C)ancel: A
```

**Prompt 3:** Share email with EFF (optional)
```
Would you be willing to share your email...
(Y)es/(N)o: N
```

**Prompt 4:** Redirect HTTP to HTTPS
```
Please choose whether or not to redirect...
1: No redirect
2: Redirect - Make all requests redirect to HTTPS
Select: 2
```

✅ **Always choose option 2!**

### Step 3: Verify SSL Installation

Visit: `https://yourdomain.com` (note the **https**)

✅ You should see:
- 🔒 Padlock icon in address bar
- Green "Secure" or "Connection is secure"
- Your registration form loading

### Step 4: Check Certificate

Click the padlock → Certificate → Details

You should see:
- Issued by: Let's Encrypt
- Valid for 90 days
- Expires on: [Date]

### Step 5: Setup Auto-Renewal

Let's Encrypt certificates expire after 90 days. Setup automatic renewal:

```bash
# Check renewal timer
sudo systemctl status certbot.timer

# If not active, enable it
sudo systemctl enable certbot.timer
sudo systemctl start certbot.timer

# Test renewal (dry run)
sudo certbot renew --dry-run
```

✅ If test succeeds, automatic renewal is configured!

---

## ✅ Final Testing

### 1. Access Website

Visit: `https://yourdomain.com`

- [ ] Page loads correctly
- [ ] SSL certificate works (padlock icon)
- [ ] All university logos display
- [ ] Responsive design works (resize browser)

### 2. Test Registration Form

- [ ] Fill Step 1 (Personal Information)
- [ ] Fill Step 2 (Program Selection)
- [ ] Upload files in Step 3
- [ ] Submit form
- [ ] See success message

### 3. Test Admin Login

- [ ] Click "Admin" link in footer
- [ ] Login with: `edu1` / `edubahrain`
- [ ] Dashboard loads
- [ ] Statistics display
- [ ] Registration appears in table

### 4. Test Admin Features

- [ ] View registration details
- [ ] View uploaded files
- [ ] Download uploaded files
- [ ] Generate email
- [ ] Copy email to clipboard
- [ ] Export to CSV

### 5. Test All Admin Users

- [ ] Login as `edu1`
- [ ] Login as `edu2`
- [ ] Login as `edu3`
- [ ] Login as `edu4`
- [ ] Login as `edu5`

### 6. Mobile Testing

- [ ] Visit on mobile phone
- [ ] Test form submission
- [ ] Test admin dashboard
- [ ] Check responsive layout

### 7. Browser Testing

- [ ] Chrome/Brave
- [ ] Firefox
- [ ] Safari
- [ ] Edge

---

## 🐛 Troubleshooting

### Problem: Can't Access Website

**Check 1: DNS**
```bash
dig yourdomain.com
```
Solution: Wait for DNS propagation (up to 48 hours)

**Check 2: Nginx Running**
```bash
sudo systemctl status nginx
```
Solution: Restart Nginx: `sudo systemctl restart nginx`

**Check 3: Firewall**
```bash
sudo ufw status
```
Solution: Allow Nginx: `sudo ufw allow 'Nginx Full'`

### Problem: "502 Bad Gateway"

**Solution:**
```bash
# Check Nginx error logs
sudo tail -50 /var/log/nginx/error.log

# Restart Nginx
sudo systemctl restart nginx
```

### Problem: "403 Forbidden"

**Solution: Fix Permissions**
```bash
cd /var/www/html
sudo chmod 644 *.html *.png
sudo chmod 755 .
sudo chown -R www-data:www-data /var/www/html
sudo systemctl restart nginx
```

### Problem: SSL Certificate Error

**Solution: Reinstall Certificate**
```bash
sudo certbot delete
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

### Problem: Images Not Loading

**Check 1: Files Exist**
```bash
ls -la /var/www/html/*.png
```

**Check 2: File Names**
- Make sure file names in HTML match actual files
- Check for spaces in file names

**Solution: Re-upload Images**
```bash
scp /Users/ahr/Downloads/*.png root@YOUR_DROPLET_IP:/var/www/html/
```

### Problem: File Upload Not Working

**Reason:** localStorage is browser-based, not server-based
**Note:** This is by design - files are stored in user's browser

### Problem: Can't SSH Into Server

**Solution 1: Reset Root Password**
- Go to DigitalOcean dashboard
- Click droplet → "Access" → "Reset Root Password"
- Check email for new password

**Solution 2: Use Recovery Console**
- DigitalOcean dashboard → "Access" → "Launch Recovery Console"
- Access server through browser

---

## 📊 Monitoring

### View Access Logs

```bash
# Last 50 requests
sudo tail -50 /var/log/nginx/access.log

# Follow in real-time
sudo tail -f /var/log/nginx/access.log
```

### View Error Logs

```bash
# Last 50 errors
sudo tail -50 /var/log/nginx/error.log

# Follow in real-time
sudo tail -f /var/log/nginx/error.log
```

### Check Server Resources

```bash
# Disk usage
df -h

# Memory usage
free -h

# CPU and processes
top
# Press 'q' to exit

# Network usage
vnstat
# Install if needed: sudo apt install vnstat -y
```

### DigitalOcean Monitoring

1. Go to: https://cloud.digitalocean.com
2. Click your droplet
3. Click "Graphs" tab
4. View:
   - CPU usage
   - Disk I/O
   - Network traffic
   - Memory usage

---

## 🔐 Security Hardening

### Install Fail2Ban

Protects against brute-force attacks:

```bash
# Install
sudo apt install fail2ban -y

# Enable and start
sudo systemctl enable fail2ban
sudo systemctl start fail2ban

# Check status
sudo fail2ban-client status
```

### Configure SSH (Recommended)

```bash
# Edit SSH config
sudo nano /etc/ssh/sshd_config

# Change these settings:
PermitRootLogin no          # Disable root login
PasswordAuthentication no    # Use SSH keys only (if you have them)
Port 2222                    # Change default SSH port (optional)

# Save and restart SSH
sudo systemctl restart sshd
```

⚠️ **Warning:** Only disable password auth if you have SSH keys set up!

### Enable Automatic Security Updates

```bash
sudo apt install unattended-upgrades -y
sudo dpkg-reconfigure -plow unattended-upgrades
# Select "Yes"
```

---

## 💾 Backup Strategy

### Enable DigitalOcean Backups

1. Go to droplet page
2. Click "Backups"
3. Click "Enable Backups" (+$1.20/month)
4. Backups run weekly automatically

### Manual Backup

```bash
# Backup files
sudo tar -czf ~/eduguide-backup-$(date +%Y%m%d).tar.gz /var/www/html

# Download backup to your computer
scp root@YOUR_DROPLET_IP:~/eduguide-backup-*.tar.gz ~/Downloads/
```

### Restore from Backup

```bash
# Upload backup to server
scp ~/Downloads/eduguide-backup-*.tar.gz root@YOUR_DROPLET_IP:~/

# Extract
sudo tar -xzf ~/eduguide-backup-*.tar.gz -C /
```

---

## 📝 Maintenance

### Weekly Tasks

```bash
# Check for security updates
sudo apt update && sudo apt upgrade -y

# Check disk space
df -h

# Check error logs
sudo tail -50 /var/log/nginx/error.log

# Verify SSL certificate
sudo certbot certificates
```

### Monthly Tasks

- Export all registrations to CSV
- Review access logs for unusual activity
- Test backup restoration procedure
- Update admin passwords if needed

---

## 🎉 Deployment Complete!

Your EduGuide Registration System is now live at:
**https://yourdomain.com**

### What's Working:
✅ Student registration form
✅ File uploads and storage
✅ Admin dashboard (5 users)
✅ Email template generator
✅ CSV export
✅ SSL/HTTPS encryption
✅ Automatic backups
✅ Security measures

---

## 📞 Support Resources

**DigitalOcean:**
- Docs: https://docs.digitalocean.com
- Community: https://community.digitalocean.com
- Support: https://cloud.digitalocean.com/support

**Nginx:**
- Docs: https://nginx.org/en/docs/

**Let's Encrypt:**
- Docs: https://letsencrypt.org/docs/

**EduGuide Support:**
- Email: ar@eduguidebh.com
- Website: www.eduguidebh.com

---

**Deployment Date:** ________________
**Domain:** ________________
**Server IP:** ________________
**Admin Credentials:** (Store securely!)
