#!/bin/bash
# Run this on your DigitalOcean server to download and install updated files

echo "🚀 Downloading updated files..."

# Backup existing files
cd /var/www/html
cp registration-form.html registration-form.html.backup.$(date +%Y%m%d)
cp admin-dashboard-professional.html admin-dashboard-professional.html.backup.$(date +%Y%m%d)

echo "✅ Backups created"

# You can either:
# 1. Manually edit the files (change API endpoints)
# 2. Use scp to upload from your Mac (need SSH key)
# 3. Copy-paste the full file content

echo "📝 To manually edit:"
echo "  nano registration-form.html"
echo "  Change: '/api/submit-registration.php' to '/api-submit-phd-registration.php'"
echo ""
echo "  nano admin-dashboard-professional.html"  
echo "  Change: '/api/get-registrations.php' to '/api-get-registrations.php'"
echo ""
echo "✅ Done!"

