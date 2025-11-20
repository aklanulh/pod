# 🔧 PRODUCTION TROUBLESHOOTING - ERROR 500

**Issue:** Error 500 pada multiple pages:
- `/kso-roi/customer/2`
- `/reports`

**Status:** Debugging in progress

---

## 🚨 IMMEDIATE ACTIONS

### **Step 1: SSH to Production Server**
```bash
ssh u919556019@id-dci-web1365
cd /home/u919556019/domains/msapt.co.id/public_html/pod
```

### **Step 2: Check Laravel Error Log**
```bash
# View last 100 lines of error log
tail -100 storage/logs/laravel.log

# Or follow real-time errors
tail -f storage/logs/laravel.log
```

### **Step 3: Verify Database Connection**
```bash
# Test MySQL connection
mysql -h localhost -u u919556019_supermsa -p u919556019_wms

# Enter password: Aa153456!
# If successful, you'll see: mysql>
# Type: exit
```

### **Step 4: Check Migration Status**
```bash
php artisan migrate:status
```

### **Step 5: Verify .env Configuration**
```bash
# Check database configuration
cat .env | grep DB_

# Should show:
# DB_CONNECTION=mysql
# DB_HOST=localhost
# DB_PORT=3306
# DB_DATABASE=u919556019_wms
# DB_USERNAME=u919556019_supermsa
# DB_PASSWORD=Aa153456!
```

---

## 🔍 COMMON ISSUES & SOLUTIONS

### **Issue 1: Database Connection Failed**

**Symptoms:**
- Error: "SQLSTATE[HY000]: General error: connection refused"
- Error: "Access denied for user"

**Solution:**
```bash
# 1. Verify MySQL is running
mysql -h localhost -u u919556019_supermsa -p u919556019_wms

# 2. Check .env file
cat .env | grep DB_

# 3. If credentials wrong, update .env
nano .env
# Update DB_USERNAME and DB_PASSWORD
# Save: Ctrl+O, Enter, Ctrl+X

# 4. Clear cache
php artisan config:clear
php artisan config:cache
```

### **Issue 2: Migrations Not Run**

**Symptoms:**
- Error: "SQLSTATE[HY000]: General error: no such table: stock_movements"
- Error: "no such table: customers"

**Solution:**
```bash
# Check migration status
php artisan migrate:status

# If not all migrated, run:
php artisan migrate:fresh --seed

# Verify tables created
mysql -h localhost -u u919556019_supermsa -p u919556019_wms
SHOW TABLES;
exit
```

### **Issue 3: Missing Environment Variables**

**Symptoms:**
- Error: "Undefined index: APP_KEY"
- Error: "Call to undefined method"

**Solution:**
```bash
# Generate APP_KEY if missing
php artisan key:generate

# Clear cache
php artisan config:clear
php artisan config:cache
```

### **Issue 4: File Permissions**

**Symptoms:**
- Error: "Permission denied" in storage logs
- Error: "Cannot write to storage"

**Solution:**
```bash
# Set proper permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chmod 644 .env
```

---

## 📊 FULL DIAGNOSTIC SCRIPT

Run this command to get all diagnostic info at once:

```bash
#!/bin/bash
echo "=== Laravel Version ==="
php artisan --version

echo -e "\n=== Environment ==="
cat .env | grep APP_

echo -e "\n=== Database Configuration ==="
cat .env | grep DB_

echo -e "\n=== Database Connection Test ==="
php artisan tinker
# Type: DB::connection()->getPdo();
# If successful, shows PDO object
# Type: exit

echo -e "\n=== Migration Status ==="
php artisan migrate:status

echo -e "\n=== Recent Errors (Last 50 lines) ==="
tail -50 storage/logs/laravel.log

echo -e "\n=== File Permissions ==="
ls -la storage/
ls -la bootstrap/cache/
```

---

## 🎯 STEP-BY-STEP RECOVERY

If everything is broken, follow this:

```bash
# 1. SSH to server
ssh u919556019@id-dci-web1365

# 2. Navigate to app
cd /home/u919556019/domains/msapt.co.id/public_html/pod

# 3. Verify .env exists
ls -la .env

# 4. If .env missing, create it:
cat > .env << 'EOF'
APP_NAME="POD - MSA"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://pod.msapt.co.id

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

# 5. Generate APP_KEY
php artisan key:generate

# 6. Clear cache
php artisan config:clear
php artisan cache:clear

# 7. Run migrations
php artisan migrate:fresh --seed

# 8. Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 9. Set permissions
chmod -R 775 storage bootstrap/cache
chmod 644 .env

# 10. Test
php artisan tinker
# Type: DB::connection()->getPdo();
# Should show PDO object
# Type: exit
```

---

## 🔗 AFFECTED ROUTES

Routes that are currently erroring:

```
GET /reports                    - ReportController@index
GET /reports/stock              - ReportController@stockReport
GET /reports/customer           - ReportController@customerReport
GET /kso-roi/customer/{id}      - KsoRoiController@customerDetail
```

All require:
- ✅ Database connection
- ✅ Migrations run
- ✅ .env configured
- ✅ Proper permissions

---

## 📞 NEXT STEPS

1. **SSH to production server**
2. **Run diagnostic commands above**
3. **Share error log output** (tail -100 storage/logs/laravel.log)
4. **Share migration status** (php artisan migrate:status)
5. **I'll help fix based on actual error**

---

## 🆘 IF STILL STUCK

Contact Hostinger support if:
- MySQL service is down
- Cannot connect to MySQL
- File permissions cannot be changed
- Need to create database

---

**Status:** Ready for debugging
**Server:** Hostinger (id-dci-web1365)
**Database:** MySQL (u919556019_wms)
**User:** u919556019_supermsa
