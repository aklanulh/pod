# 🗄️ MYSQL SETUP - HOSTINGER

**Server:** id-dci-web1365
**Domain:** msapt.co.id
**User:** u919556019

---

## 📋 DATABASE CREDENTIALS

### Primary Database
```
Database: u919556019_wms
Username: u919556019_supermsa
Password: Aa153456!
Host: localhost
Port: 3306
```

### Connection String
```
mysql://u919556019_supermsa:Aa153456!@localhost:3306/u919556019_wms
```

---

## 🔧 LARAVEL .env CONFIGURATION

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u919556019_wms
DB_USERNAME=u919556019_supermsa
DB_PASSWORD=Aa153456!
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
```

---

## ✅ SETUP STEPS

### Step 1: SSH to Hostinger
```bash
ssh u919556019@id-dci-web1365
```

### Step 2: Navigate to App
```bash
cd /home/u919556019/domains/msapt.co.id/public_html/pod
```

### Step 3: Create .env File
```bash
cat > .env << 'EOF'
APP_NAME="POD - MSA"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://wms.msapt.co.id

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u919556019_wms
DB_USERNAME=u919556019_supermsa
DB_PASSWORD=Aa153456!
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci

LOG_CHANNEL=stack
LOG_LEVEL=error

CACHE_DRIVER=file
SESSION_DRIVER=cookie
QUEUE_CONNECTION=sync

MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@msapt.co.id
MAIL_FROM_NAME="POD - MSA"

QR_PASSWORD=MSA2008
EOF
```

### Step 4: Generate APP_KEY
```bash
php artisan key:generate
```

### Step 5: Run Migrations
```bash
php artisan migrate:fresh --seed
```

### Step 6: Set Permissions
```bash
chmod -R 775 storage bootstrap/cache
chmod 644 .env
```

### Step 7: Clear Cache
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🔍 VERIFICATION

### Test MySQL Connection
```bash
mysql -h localhost -u u919556019_supermsa -p u919556019_wms
# Enter password: Aa153456!
```

### Check Tables Created
```sql
USE u919556019_wms;
SHOW TABLES;
```

### Verify Migrations
```bash
php artisan migrate:status
```

### Check Configuration
```bash
php artisan config:show | grep DB_
```

---

## 📊 DATABASE TABLES

After migration, these tables should exist:

```
users
customers
products
product_categories
suppliers
stock_movements
stock_opnames
stock_opname_details
customer_schedules
stock_out_drafts
kso_items
kso_support_items
admin_activity_logs
migrations
cache
jobs
```

---

## 🎯 TROUBLESHOOTING

### Connection Refused
```bash
# Check if MySQL is running
mysql -h localhost -u u919556019_supermsa -p

# If error, contact Hostinger support
```

### Permission Denied
```bash
# Fix permissions
chmod -R 775 storage bootstrap/cache
chmod 644 .env
```

### Database Does Not Exist
```bash
# Create database via Hostinger cPanel
# Or use MySQL command:
mysql -h localhost -u u919556019_supermsa -p
CREATE DATABASE u919556019_wms;
```

### Migration Errors
```bash
# Clear cache first
php artisan config:clear
php artisan cache:clear

# Then run migrations
php artisan migrate:fresh --seed
```

---

## 🔐 SECURITY CHECKLIST

- [ ] APP_DEBUG set to false
- [ ] APP_KEY generated and unique
- [ ] Database password secure
- [ ] .env file permissions 644
- [ ] Storage permissions 775
- [ ] HTTPS enabled
- [ ] Regular backups scheduled

---

## 📞 HOSTINGER SUPPORT

If you need to:
- Create new database
- Reset database password
- Check MySQL version
- Enable remote access

Contact Hostinger support or use cPanel.

---

## 🚀 QUICK COMMANDS

```bash
# SSH
ssh u919556019@id-dci-web1365

# Navigate
cd /home/u919556019/domains/msapt.co.id/public_html/pod

# Generate key
php artisan key:generate

# Migrate
php artisan migrate:fresh --seed

# Cache
php artisan config:cache

# Verify
php artisan migrate:status
```

---

**Database:** ✅ u919556019_wms
**User:** ✅ u919556019_supermsa
**Status:** Ready for deployment
