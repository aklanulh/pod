# 🚀 PRODUCTION SETUP - POD (Platform Operating Digital)

**Date:** November 20, 2025
**Server:** Hostinger (id-dci-web1365)
**Domain:** wms.msapt.co.id → pod.msapt.co.id

---

## 📋 DATABASE CREDENTIALS (Hostinger)

### MySQL Database
```
Database Name: u919556019_wms
Username: u919556019_supermsa
Password: Aa153456!
Host: localhost
Port: 3306
```

### Alternative Database (if needed)
```
Database Name: u919556019_pod
Username: u919556019_supermsa
Password: Aa153456!
```

---

## 🔧 PRODUCTION .env CONFIGURATION

Create `.env` file on production server with:

```env
# Application
APP_NAME="POD - MSA"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://wms.msapt.co.id

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error

# Database - MySQL (Recommended for Production)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u919556019_wms
DB_USERNAME=u919556019_supermsa
DB_PASSWORD=Aa153456!
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci

# Cache
CACHE_DRIVER=file
CACHE_STORE=file

# Session
SESSION_DRIVER=cookie
SESSION_LIFETIME=120

# Queue
QUEUE_CONNECTION=sync

# Mail (if needed)
MAIL_MAILER=log
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@msapt.co.id
MAIL_FROM_NAME="POD - MSA"

# QR Code Password
QR_PASSWORD=MSA2008
```

---

## 📝 PRODUCTION DEPLOYMENT STEPS

### Step 1: SSH to Server
```bash
ssh u919556019@id-dci-web1365
```

### Step 2: Navigate to App Directory
```bash
cd /home/u919556019/domains/msapt.co.id/public_html/pod
```

### Step 3: Create .env File
```bash
# Copy from .env.example or create manually
nano .env

# Paste the configuration above
# Save: Ctrl+O, Enter, Ctrl+X
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
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chmod 644 .env
```

### Step 7: Verify Installation
```bash
php artisan migrate:status
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🔍 TROUBLESHOOTING

### Error: Database does not exist
**Solution:** Create database file first
```bash
touch database/database.sqlite
chmod 666 database/database.sqlite
```

### Error: Permission denied
**Solution:** Fix permissions
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Error: Connection refused
**Solution:** Check MySQL credentials
```bash
mysql -h localhost -u u919556019_supermsa -p
# Enter password: Aa153456!
```

### Error: SQLSTATE[HY000]: General error
**Solution:** Clear cache and regenerate
```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

---

## 📊 DATABASE VERIFICATION

### Check MySQL Connection
```bash
mysql -h localhost -u u919556019_supermsa -p u919556019_wms
```

### List Databases
```sql
SHOW DATABASES;
```

### Check Tables
```sql
USE u919556019_wms;
SHOW TABLES;
```

### Verify Migrations
```bash
php artisan migrate:status
```

---

## 🎯 POST-DEPLOYMENT CHECKLIST

- [ ] SSH to production server
- [ ] Navigate to app directory
- [ ] Create .env file with MySQL credentials
- [ ] Run `php artisan key:generate`
- [ ] Run `php artisan migrate:fresh --seed`
- [ ] Set proper permissions (775 for storage)
- [ ] Clear cache: `php artisan config:cache`
- [ ] Test application: Visit https://wms.msapt.co.id
- [ ] Verify login works
- [ ] Check database tables created
- [ ] Test KSO ROI functionality
- [ ] Test QR Code access

---

## 🔐 SECURITY NOTES

1. **APP_KEY:** Generate unique key per environment
2. **Database Password:** Keep secure, don't commit to git
3. **APP_DEBUG:** Always set to `false` in production
4. **Permissions:** Set 775 for storage, 644 for .env
5. **HTTPS:** Ensure SSL certificate is active
6. **Backups:** Regular database backups recommended

---

## 📞 SUPPORT

If you encounter issues:

1. Check error logs: `tail -f storage/logs/laravel.log`
2. Verify MySQL connection: `mysql -h localhost -u u919556019_supermsa -p`
3. Check file permissions: `ls -la storage/`
4. Review .env configuration: `cat .env | grep DB_`

---

## 🚀 QUICK REFERENCE

### Full Deployment Command
```bash
cd /home/u919556019/domains/msapt.co.id/public_html/pod
php artisan key:generate
php artisan migrate:fresh --seed
php artisan config:cache
php artisan route:cache
php artisan view:cache
chmod -R 775 storage bootstrap/cache
```

### Verify Status
```bash
php artisan migrate:status
php artisan config:show | grep DB_
```

---

**Status:** ✅ Ready for Production Deployment
**Database:** MySQL (u919556019_wms)
**User:** u919556019_supermsa
**Domain:** wms.msapt.co.id
