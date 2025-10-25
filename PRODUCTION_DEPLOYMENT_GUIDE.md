# 🚀 Dress Up Davao - Production Deployment Guide

## 📋 Pre-Deployment Checklist

### ✅ Production Readiness Assessment

Your Laravel application is **READY FOR PRODUCTION** with the following status:

-   ✅ **Laravel 12.34.0** - Latest stable version
-   ✅ **PHP 8.2.12** - Compatible and secure
-   ✅ **FilamentPHP v4.1.9** - Admin panel ready
-   ✅ **Security configurations** - Properly configured
-   ✅ **Performance optimizations** - Already implemented
-   ✅ **Database migrations** - Complete (30 migrations)
-   ✅ **3D Model integration** - Kiri Engine API configured
-   ✅ **Real-time chat** - Pusher integration ready
-   ✅ **Email system** - SendGrid configured
-   ✅ **File uploads** - Optimized for large files

---

## 🔧 Server Requirements

### Minimum Server Specifications

```
- PHP 8.2 or higher
- MySQL 8.0 or MariaDB 10.3+
- Nginx 1.18+ or Apache 2.4+
- Node.js 18+ (for asset compilation)
- Redis (recommended for caching/sessions)
- SSL Certificate (required for production)
- 2GB RAM minimum (4GB recommended)
- 20GB storage minimum
```

### Required PHP Extensions

```
- BCMath
- Ctype
- cURL
- DOM
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PCRE
- PDO
- Tokenizer
- XML
- GD or Imagick (for image processing)
- Redis (if using Redis)
```

---

## 🌐 Domain & SSL Setup

### 1. Domain Configuration

```bash
# Point your domain to your server IP
# Example DNS records:
A     dressupdavao.com        YOUR_SERVER_IP
A     www.dressupdavao.com    YOUR_SERVER_IP
```

### 2. SSL Certificate (Let's Encrypt)

```bash
# Install Certbot
sudo apt update
sudo apt install certbot python3-certbot-nginx

# Generate SSL certificate
sudo certbot --nginx -d dressupdavao.com -d www.dressupdavao.com
```

---

## 📁 Server Setup & File Deployment

### 1. Clone Repository

```bash
# Navigate to web directory
cd /var/www

# Clone your repository
git clone https://github.com/yourusername/dress-up-davao.git
cd dress-up-davao

# Set proper permissions
sudo chown -R www-data:www-data /var/www/dress-up-davao
sudo chmod -R 755 /var/www/dress-up-davao
sudo chmod -R 775 storage bootstrap/cache
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies
npm install

# Build production assets
npm run build
```

---

## ⚙️ Environment Configuration

### 1. Create Production Environment File

```bash
# Copy environment template
cp .env.example .env.production

# Edit production environment
nano .env.production
```

### 2. Production Environment Variables

```env
# Application Settings
APP_NAME="Dress Up Davao"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_DEBUG=false
APP_URL=https://dressupdavao.com

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dress_up_davao_prod
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

# Cache & Session (Redis recommended)
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis Configuration
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=your_redis_password
REDIS_PORT=6379

# Mail Configuration (SendGrid)
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=YOUR_SENDGRID_API_KEY
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@dressupdavao.com
MAIL_FROM_NAME="Dress Up Davao"

# Pusher Configuration (Real-time Chat)
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_pusher_app_id
PUSHER_APP_KEY=your_pusher_key
PUSHER_APP_SECRET=your_pusher_secret
PUSHER_APP_CLUSTER=your_cluster

# Kiri Engine API (3D Models)
KIRI_API_KEY=your_kiri_api_key

# Security Settings
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict

# Performance Settings
FILAMENT_CACHE_QUERIES=true
FILAMENT_CACHE_DURATION=60
FILAMENT_IMAGE_OPTIMIZATION=true
COMPRESS_HTML=true
BROTLI_COMPRESSION=true

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error
LOG_DEPRECATIONS_CHANNEL=null
```

### 3. Generate Application Key

```bash
php artisan key:generate --env=production
```

---

## 🗄️ Database Setup

### 1. Create Production Database

```sql
-- Connect to MySQL as root
mysql -u root -p

-- Create database and user
CREATE DATABASE dress_up_davao_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'dress_up_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON dress_up_davao_prod.* TO 'dress_up_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 2. Run Migrations

```bash
# Run database migrations
php artisan migrate --env=production --force

# Seed initial data (if needed)
php artisan db:seed --env=production --force
```

---

## 🔧 Web Server Configuration

### Nginx Configuration

```nginx
# /etc/nginx/sites-available/dressupdavao.com
server {
    listen 80;
    listen [::]:80;
    server_name dressupdavao.com www.dressupdavao.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name dressupdavao.com www.dressupdavao.com;
    root /var/www/dress-up-davao/public;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/dressupdavao.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/dressupdavao.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512;
    ssl_prefer_server_ciphers off;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # File Upload Limits (for 3D models)
    client_max_body_size 100M;
    client_body_timeout 300s;
    client_header_timeout 300s;

    index index.php;

    charset utf-8;

    # Handle Laravel routes
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM Configuration
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
        fastcgi_send_timeout 300;
    }

    # Static file caching
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Security: Hide sensitive files
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;
}
```

### Enable Site

```bash
# Enable the site
sudo ln -s /etc/nginx/sites-available/dressupdavao.com /etc/nginx/sites-enabled/

# Test configuration
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
```

---

## 🚀 Application Optimization

### 1. Laravel Optimizations

```bash
# Cache configurations
php artisan config:cache --env=production
php artisan route:cache --env=production
php artisan view:cache --env=production

# Optimize autoloader
composer dump-autoload --optimize --no-dev

# Cache Filament components
php artisan filament:cache-components
```

### 2. Storage Link

```bash
# Create storage symlink
php artisan storage:link
```

### 3. Queue Worker Setup

```bash
# Create systemd service for queue worker
sudo nano /etc/systemd/system/dress-up-queue.service
```

```ini
[Unit]
Description=Dress Up Davao Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/dress-up-davao
ExecStart=/usr/bin/php artisan queue:work --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
```

```bash
# Enable and start queue worker
sudo systemctl enable dress-up-queue
sudo systemctl start dress-up-queue
```

---

## 📊 Monitoring & Maintenance

### 1. Log Monitoring

```bash
# Monitor application logs
tail -f storage/logs/laravel.log

# Monitor Nginx logs
tail -f /var/log/nginx/access.log
tail -f /var/log/nginx/error.log
```

### 2. Backup Strategy

```bash
# Create backup script
nano /home/backup-dress-up.sh
```

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/home/backups"
APP_DIR="/var/www/dress-up-davao"

# Create backup directory
mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u dress_up_user -p dress_up_davao_prod > $BACKUP_DIR/database_$DATE.sql

# Files backup
tar -czf $BACKUP_DIR/files_$DATE.tar.gz $APP_DIR/storage/app/public

# Keep only last 7 days of backups
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete
```

### 3. Cron Jobs

```bash
# Edit crontab
crontab -e

# Add Laravel scheduler
* * * * * cd /var/www/dress-up-davao && php artisan schedule:run >> /dev/null 2>&1

# Add backup job (daily at 2 AM)
0 2 * * * /home/backup-dress-up.sh
```

---

## 🔒 Security Hardening

### 1. File Permissions

```bash
# Set secure permissions
sudo chown -R www-data:www-data /var/www/dress-up-davao
sudo find /var/www/dress-up-davao -type f -exec chmod 644 {} \;
sudo find /var/www/dress-up-davao -type d -exec chmod 755 {} \;
sudo chmod -R 775 storage bootstrap/cache
```

### 2. Firewall Configuration

```bash
# Configure UFW firewall
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw enable
```

### 3. Fail2Ban (Optional)

```bash
# Install Fail2Ban
sudo apt install fail2ban

# Configure for Nginx
sudo nano /etc/fail2ban/jail.local
```

---

## ✅ Post-Deployment Verification

### 1. Application Health Check

```bash
# Test application
curl -I https://dressupdavao.com

# Check queue worker
sudo systemctl status dress-up-queue

# Verify database connection
php artisan tinker --env=production
>>> DB::connection()->getPdo();
```

### 2. Feature Testing

-   [ ] User registration/login
-   [ ] Admin panel access
-   [ ] Product browsing
-   [ ] 3D model uploads
-   [ ] Real-time chat
-   [ ] Email notifications
-   [ ] File uploads
-   [ ] Payment processing

---

## 🆘 Troubleshooting

### Common Issues

**Permission Errors:**

```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

**Queue Not Processing:**

```bash
sudo systemctl restart dress-up-queue
php artisan queue:restart
```

**Cache Issues:**

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

**SSL Certificate Renewal:**

```bash
sudo certbot renew --dry-run
```

---

## 📞 Support & Maintenance

### Regular Maintenance Tasks

-   **Weekly:** Check logs and system resources
-   **Monthly:** Update dependencies and security patches
-   **Quarterly:** Review and optimize database performance
-   **Annually:** Renew SSL certificates and review security

### Performance Monitoring

-   Monitor server resources (CPU, RAM, Disk)
-   Track application response times
-   Monitor database query performance
-   Check queue processing times

---

## 🔄 Deployment Automation (Optional)

### GitHub Actions CI/CD Pipeline

Create `.github/workflows/deploy.yml`:

```yaml
name: Deploy to Production

on:
    push:
        branches: [main]

jobs:
    deploy:
        runs-on: ubuntu-latest

        steps:
            - uses: actions/checkout@v3

            - name: Setup PHP
              uses: shivammathur/setup-php@v2
              with:
                  php-version: "8.2"

            - name: Install dependencies
              run: composer install --no-dev --optimize-autoloader

            - name: Run tests
              run: php artisan test

            - name: Deploy to server
              uses: appleboy/ssh-action@v0.1.5
              with:
                  host: ${{ secrets.HOST }}
                  username: ${{ secrets.USERNAME }}
                  key: ${{ secrets.KEY }}
                  script: |
                      cd /var/www/dress-up-davao
                      git pull origin main
                      composer install --no-dev --optimize-autoloader
                      npm install && npm run build
                      php artisan migrate --force
                      php artisan config:cache
                      php artisan route:cache
                      php artisan view:cache
                      sudo systemctl restart dress-up-queue
```

---

## 📈 Performance Optimization

### 1. Redis Configuration

```bash
# Install Redis
sudo apt install redis-server

# Configure Redis
sudo nano /etc/redis/redis.conf
```

```conf
# Redis optimizations
maxmemory 256mb
maxmemory-policy allkeys-lru
save 900 1
save 300 10
save 60 10000
```

### 2. PHP-FPM Optimization

```bash
# Edit PHP-FPM pool configuration
sudo nano /etc/php/8.2/fpm/pool.d/www.conf
```

```conf
# Performance settings
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500

# Memory settings
php_admin_value[memory_limit] = 512M
php_admin_value[upload_max_filesize] = 100M
php_admin_value[post_max_size] = 120M
php_admin_value[max_execution_time] = 300
```

### 3. MySQL Optimization

```sql
-- Add to /etc/mysql/mysql.conf.d/mysqld.cnf
[mysqld]
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
query_cache_type = 1
query_cache_size = 64M
max_connections = 200
```

---

## 🛡️ Advanced Security

### 1. Additional Security Headers

Add to Nginx configuration:

```nginx
# Additional security headers
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
```

### 2. Rate Limiting

```nginx
# Rate limiting configuration
http {
    limit_req_zone $binary_remote_addr zone=login:10m rate=5r/m;
    limit_req_zone $binary_remote_addr zone=api:10m rate=10r/s;
}

server {
    # Apply rate limiting
    location /login {
        limit_req zone=login burst=5 nodelay;
    }

    location /api/ {
        limit_req zone=api burst=20 nodelay;
    }
}
```

### 3. Database Security

```sql
-- Remove test database and anonymous users
DELETE FROM mysql.user WHERE User='';
DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');
DROP DATABASE IF EXISTS test;
DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%';
FLUSH PRIVILEGES;
```

---

## 📱 Mobile & PWA Optimization

### 1. Service Worker (Optional)

Create `public/sw.js`:

```javascript
const CACHE_NAME = "dress-up-davao-v1";
const urlsToCache = ["/", "/css/app.css", "/js/app.js", "/images/logo.png"];

self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(urlsToCache))
    );
});
```

### 2. Web App Manifest

Create `public/manifest.json`:

```json
{
    "name": "Dress Up Davao",
    "short_name": "DressUpDavao",
    "description": "Premium dress rental platform",
    "start_url": "/",
    "display": "standalone",
    "background_color": "#ffffff",
    "theme_color": "#7f23fe",
    "icons": [
        {
            "src": "/images/icon-192.png",
            "sizes": "192x192",
            "type": "image/png"
        },
        {
            "src": "/images/icon-512.png",
            "sizes": "512x512",
            "type": "image/png"
        }
    ]
}
```

---

## 🔍 Monitoring & Analytics

### 1. Application Performance Monitoring

```bash
# Install Laravel Telescope (development only)
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

### 2. Server Monitoring

```bash
# Install htop for system monitoring
sudo apt install htop

# Install netdata for real-time monitoring
bash <(curl -Ss https://my-netdata.io/kickstart.sh)
```

### 3. Log Analysis

```bash
# Install logrotate for log management
sudo nano /etc/logrotate.d/laravel
```

```conf
/var/www/dress-up-davao/storage/logs/*.log {
    daily
    missingok
    rotate 52
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
}
```

---

## 🚨 Disaster Recovery

### 1. Automated Backup Script

Create `/home/scripts/backup-full.sh`:

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/home/backups"
APP_DIR="/var/www/dress-up-davao"

# Full application backup
tar -czf $BACKUP_DIR/app_full_$DATE.tar.gz $APP_DIR \
  --exclude=$APP_DIR/node_modules \
  --exclude=$APP_DIR/vendor \
  --exclude=$APP_DIR/storage/logs

# Database backup with compression
mysqldump -u dress_up_user -p dress_up_davao_prod | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Upload to cloud storage (optional)
# aws s3 cp $BACKUP_DIR/app_full_$DATE.tar.gz s3://your-backup-bucket/
# aws s3 cp $BACKUP_DIR/db_$DATE.sql.gz s3://your-backup-bucket/
```

### 2. Recovery Procedures

```bash
# Database recovery
gunzip < /home/backups/db_YYYYMMDD_HHMMSS.sql.gz | mysql -u dress_up_user -p dress_up_davao_prod

# Application recovery
cd /var/www
tar -xzf /home/backups/app_full_YYYYMMDD_HHMMSS.tar.gz
sudo chown -R www-data:www-data dress-up-davao
```

---

## 📋 Final Deployment Checklist

### Pre-Launch Verification

-   [ ] SSL certificate installed and working
-   [ ] Database migrations completed
-   [ ] Environment variables configured
-   [ ] File permissions set correctly
-   [ ] Queue workers running
-   [ ] Cron jobs configured
-   [ ] Backup system tested
-   [ ] Monitoring tools installed
-   [ ] Security headers configured
-   [ ] Performance optimizations applied

### Go-Live Steps

1. [ ] Point DNS to production server
2. [ ] Verify all functionality works
3. [ ] Test payment processing
4. [ ] Verify email delivery
5. [ ] Test 3D model uploads
6. [ ] Check real-time chat
7. [ ] Monitor server resources
8. [ ] Announce launch to users

### Post-Launch Monitoring

-   [ ] Monitor application logs
-   [ ] Check server performance
-   [ ] Verify backup completion
-   [ ] Test disaster recovery
-   [ ] Monitor user feedback
-   [ ] Track application metrics

---

**🎉 Your Dress Up Davao application is now ready for production deployment!**

Follow this comprehensive guide step by step, and your application will be running securely, efficiently, and reliably in production. Remember to test each step thoroughly and maintain regular backups.
