# 📦 GitHub Repository Setup Guide

Complete guide to push your EduGuide Registration System to GitHub.

---

## 🎯 Prerequisites

1. **GitHub Account** - Create one at https://github.com if you don't have it
2. **Git Installed** - Check with: `git --version`
   - If not installed on Mac: `brew install git` or download from https://git-scm.com

---

## 📝 Step-by-Step GitHub Setup

### Step 1: Create GitHub Repository

1. **Go to GitHub:** https://github.com
2. **Click "+" icon** (top right) → "New repository"
3. **Fill in details:**
   - Repository name: `eduguide-registration`
   - Description: `Professional student registration system with admin dashboard`
   - Visibility: Choose **Private** or **Public**
   - ❌ **DO NOT** check "Add README" (we already have one)
   - ❌ **DO NOT** add .gitignore (we already have one)
4. **Click "Create repository"**
5. **Copy the repository URL** (e.g., `https://github.com/YOUR_USERNAME/eduguide-registration.git`)

---

### Step 2: Initialize Local Git Repository

Open Terminal and run:

```bash
# Navigate to your project folder
cd /Users/ahr/Downloads

# Initialize git
git init

# Configure your git identity (if first time)
git config --global user.name "Your Name"
git config --global user.email "your.email@example.com"
```

---

### Step 3: Add Files to Git

```bash
# Add HTML files
git add registration-form.html
git add admin-login.html
git add admin-dashboard.html
git add registration-details.html

# Add all PNG images (university logos)
git add *.png

# Add documentation
git add README.md
git add SYSTEM_GUIDE.md
git add DEPLOYMENT_CHECKLIST.md
git add GITHUB_SETUP.md
git add .gitignore

# Check what will be committed
git status
```

You should see all files listed in green.

---

### Step 4: Create Initial Commit

```bash
# Commit all files
git commit -m "Initial commit: EduGuide Registration System v1.0.0

Features:
- Multi-step registration form (Arabic/English)
- Admin dashboard with 5 user accounts
- File upload and storage system
- Email template generator
- CSV export functionality
- 11 university partner logos
- Complete deployment documentation"

# Verify commit
git log
```

---

### Step 5: Push to GitHub

```bash
# Create main branch
git branch -M main

# Add remote repository (replace with YOUR repository URL)
git remote add origin https://github.com/YOUR_USERNAME/eduguide-registration.git

# Push to GitHub
git push -u origin main
```

**If prompted for credentials:**
- Username: Your GitHub username
- Password: Use **Personal Access Token** (not your GitHub password)

---

### Step 6: Create Personal Access Token (if needed)

If GitHub asks for a password and it fails:

1. Go to: https://github.com/settings/tokens
2. Click "Generate new token" → "Generate new token (classic)"
3. Name: `eduguide-deployment`
4. Expiration: Choose duration
5. Select scopes: ✅ `repo` (all permissions)
6. Click "Generate token"
7. **Copy the token immediately** (you won't see it again!)
8. Use this token as your password when pushing

---

### Step 7: Verify Upload

1. **Go to your GitHub repository** in browser
2. **Check all files are present:**
   - ✓ 4 HTML files
   - ✓ 11 PNG images
   - ✓ 4 MD documentation files
   - ✓ .gitignore file

3. **View README.md** - Should display nicely formatted

---

## 🔄 Making Updates Later

When you need to update files:

```bash
# Navigate to project folder
cd /Users/ahr/Downloads

# Check what changed
git status

# Add changed files
git add registration-form.html    # Or specific files
git add *.html                     # Or all HTML files
git add .                          # Or everything

# Commit changes
git commit -m "Update: Brief description of changes"

# Push to GitHub
git push origin main
```

---

## 📋 Useful Git Commands

```bash
# Check repository status
git status

# View commit history
git log --oneline

# See what changed in a file
git diff registration-form.html

# Undo changes to a file (before commit)
git checkout -- registration-form.html

# Create a new branch
git checkout -b feature-name

# Switch branches
git checkout main

# View remote repository URL
git remote -v

# Pull latest changes from GitHub
git pull origin main
```

---

## 🌿 Recommended Branch Structure

For professional development:

```bash
# Create development branch
git checkout -b development
git push -u origin development

# Create feature branches
git checkout -b feature/new-email-template
# Make changes, commit
git push -u origin feature/new-email-template

# Merge to main when ready
git checkout main
git merge feature/new-email-template
git push origin main
```

---

## 📦 Repository Structure on GitHub

Your repository should look like:

```
eduguide-registration/
│
├── 📄 README.md                    (Main documentation)
├── 📄 SYSTEM_GUIDE.md              (Quick start guide)
├── 📄 DEPLOYMENT_CHECKLIST.md     (Deployment steps)
├── 📄 GITHUB_SETUP.md             (This file)
├── 📄 .gitignore                   (Git ignore rules)
│
├── 🌐 registration-form.html
├── 🌐 admin-login.html
├── 🌐 admin-dashboard.html
├── 🌐 admin-details.html
│
└── 🖼️ University Logos/
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

## 🔒 Security Best Practices

### 1. Never Commit Sensitive Data

The `.gitignore` file prevents committing:
- Passwords
- API keys
- Environment variables
- Personal data
- Test databases

### 2. Review Before Committing

```bash
# Always check what you're committing
git diff --staged

# If you see sensitive data, remove it
git reset HEAD filename
```

### 3. Remove Accidentally Committed Secrets

```bash
# If you committed passwords by mistake
git rm --cached filename
git commit -m "Remove sensitive file"
git push origin main --force
```

Then **immediately change** those passwords!

---

## 🎨 GitHub Repository Enhancements

### Add Repository Topics

On GitHub repository page:
1. Click ⚙️ "Settings"
2. In "About" section, click ⚙️ gear icon
3. Add topics: `education`, `registration-system`, `bahrain`, `university`, `admin-dashboard`

### Add Repository Description

```
Professional student registration system with admin dashboard for managing university applications in Bahrain
```

### Create Releases

When you have stable versions:

1. Go to "Releases" → "Create a new release"
2. Tag version: `v1.0.0`
3. Release title: `Version 1.0.0 - Initial Release`
4. Description: List features and changes
5. Attach compiled files if needed
6. Publish release

---

## 🐛 Troubleshooting

### Problem: "Permission denied (publickey)"

**Solution:** Use HTTPS instead of SSH
```bash
git remote set-url origin https://github.com/YOUR_USERNAME/eduguide-registration.git
```

### Problem: "Failed to push some refs"

**Solution:** Pull first, then push
```bash
git pull origin main --rebase
git push origin main
```

### Problem: "Large files" error

**Solution:** Files over 100MB need Git LFS
```bash
# For this project, images should be under 100MB
# If needed, compress images before committing
```

### Problem: Can't remember repository URL

**Solution:**
```bash
git remote -v
# Or check on GitHub repository page
```

---

## ✅ Verification Checklist

After pushing to GitHub, verify:

- [ ] Repository is created on GitHub
- [ ] All 4 HTML files visible
- [ ] All 11 PNG images visible
- [ ] All 4 MD files visible
- [ ] .gitignore file present
- [ ] README.md displays properly with formatting
- [ ] Repository description added
- [ ] Topics/tags added (optional)
- [ ] Repository visibility set correctly (Public/Private)

---

## 📞 Need Help?

- **GitHub Docs:** https://docs.github.com
- **Git Tutorial:** https://git-scm.com/doc
- **GitHub Support:** https://support.github.com

---

## 🎉 Success!

Once everything is pushed, your repository URL will be:
```
https://github.com/YOUR_USERNAME/eduguide-registration
```

Share this URL with your team or use it for DigitalOcean deployment!

---

**Next Steps:**
1. ✅ Verify all files on GitHub
2. ➡️ Follow `DEPLOYMENT_CHECKLIST.md` for DigitalOcean setup
3. 🚀 Deploy your application!
