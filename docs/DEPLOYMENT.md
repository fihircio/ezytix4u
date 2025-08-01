# Deployment Guide

## Overview
This guide provides comprehensive instructions for deploying the EzyTix4U platform to production environments. The guide covers server setup, configuration, security, and maintenance procedures.

## Prerequisites

### Server Requirements
- **Operating System**: Ubuntu 20.04 LTS or CentOS 8
- **PHP**: 8.1 or higher
- **Web Server**: Nginx 1.18+ or Apache 2.4+
- **Database**: MySQL 8.0+ or MariaDB 10.5+
- **Cache**: Redis 6.0+
- **SSL Certificate**: Valid SSL certificate
- **Domain**: Registered domain name

### Server Specifications
- **CPU**: 2+ cores
- **RAM**: 4GB+ (8GB recommended)
- **Storage**: 50GB+ SSD
- **Bandwidth**: 100Mbps+

## Server Setup

### 1. Initial Server Configuration

#### Update System
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y curl wget git unzip
```

#### Install PHP 8.1
```bash
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install -y php8.1 php8.1-fpm php8.1-mysql php8.1-redis php8.1-curl php8.1-gd php8.1-mbstring php8.1-xml php8.1-zip php8.1-bcmath
```

#### Install MySQL
```bash
sudo apt install -y mysql-server
sudo mysql_secure_installation
```

#### Install Redis
```bash
sudo apt install -y redis-server
sudo systemctl enable redis-server
```

#### Install Nginx
```bash
sudo apt install -y nginx
sudo systemctl enable nginx
```

### 2. Application Deployment

#### Clone Repository
```bash
cd /var/www
sudo git clone https://github.com/your-repo/ezytix4u.git
sudo chown -R www-data:www-data ezytix4u
cd ezytix4u
```

#### Install Dependencies
```bash
composer install --no-dev --optimize-autoloader
npm install
npm run production
```

#### Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

#### Configure Environment Variables
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ezytix4u.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ezytix4u
DB_USERNAME=ezytix4u_user
DB_PASSWORD=secure_password

# Cache
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=noreply@ezytix4u.com
MAIL_PASSWORD=app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@ezytix4u.com
MAIL_FROM_NAME="EzyTix4U"

# Payment Gateways
PAYPAL_CLIENT_ID=your_paypal_client_id
PAYPAL_SECRET=your_paypal_secret
PAYPAL_MODE=live

BILLPLZ_SECRET_KEY=your_billplz_secret_key
BILLPLZ_XSIGNATURE=your_billplz_xsignature
BILLPLZ_APP_ID=your_billplz_collection_id
BILLPLZ_REDIRECT_URI=https://ezytix4u.com/bookings/billplz/callback

TOYYIBPAY_SECRET_KEY=your_toyyibpay_secret_key
TOYYIBPAY_REDIRECT_URI=https://ezytix4u.com/bookings/toyyibpay/callback

USAEPAY_SOURCE_KEY=your_usaepay_source_key
USAEPAY_PIN=your_usaepay_pin
```

#### Database Setup
```bash
php artisan migrate --force
php artisan db:seed --force
```

#### File Permissions
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 3. Web Server Configuration

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name ezytix4u.com www.ezytix4u.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name ezytix4u.com www.ezytix4u.com;

    ssl_certificate /etc/letsencrypt/live/ezytix4u.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/ezytix4u.com/privkey.pem;

    root /var/www/ezytix4u/public;
    index index.php index.html index.htm;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private must-revalidate auth;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }

    # Cache static files
    location ~* \.(jpg|jpeg|png|gif|ico|css|js)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

#### PHP-FPM Configuration
```ini
; /etc/php/8.1/fpm/php.ini
memory_limit = 256M
max_execution_time = 60
upload_max_filesize = 64M
post_max_size = 64M
opcache.enable = 1
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 8
opcache.max_accelerated_files = 4000
```

### 4. SSL Certificate Setup

#### Install Certbot
```bash
sudo apt install -y certbot python3-certbot-nginx
```

#### Obtain SSL Certificate
```bash
sudo certbot --nginx -d ezytix4u.com -d www.ezytix4u.com
```

#### Auto-renewal
```bash
sudo crontab -e
# Add this line:
0 12 * * * /usr/bin/certbot renew --quiet
```

## Security Configuration

### 1. Firewall Setup
```bash
sudo ufw allow 22
sudo ufw allow 80
sudo ufw allow 443
sudo ufw enable
```

### 2. Database Security
```sql
-- Create database and user
CREATE DATABASE ezytix4u;
CREATE USER 'ezytix4u_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON ezytix4u.* TO 'ezytix4u_user'@'localhost';
FLUSH PRIVILEGES;
```

### 3. Application Security
```bash
# Generate application key
php artisan key:generate

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## Performance Optimization

### 1. Redis Configuration
```bash
# /etc/redis/redis.conf
maxmemory 256mb
maxmemory-policy allkeys-lru
```

### 2. MySQL Optimization
```sql
-- /etc/mysql/mysql.conf.d/mysqld.cnf
[mysqld]
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
query_cache_size = 64M
query_cache_type = 1
```

### 3. Application Caching
```bash
# Cache routes and config
php artisan route:cache
php artisan config:cache
php artisan view:cache
```

## Monitoring Setup

### 1. Log Monitoring
```bash
# Install log monitoring tools
sudo apt install -y logwatch
```

### 2. Application Monitoring
```bash
# Install Laravel Telescope (optional)
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

### 3. Server Monitoring
```bash
# Install monitoring tools
sudo apt install -y htop iotop nethogs
```

## Backup Strategy

### 1. Database Backup
```bash
#!/bin/bash
# /usr/local/bin/backup-database.sh
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u ezytix4u_user -p'secure_password' ezytix4u > /backups/database_$DATE.sql
gzip /backups/database_$DATE.sql
```

### 2. File Backup
```bash
#!/bin/bash
# /usr/local/bin/backup-files.sh
DATE=$(date +%Y%m%d_%H%M%S)
tar -czf /backups/files_$DATE.tar.gz /var/www/ezytix4u
```

### 3. Automated Backups
```bash
# Add to crontab
0 2 * * * /usr/local/bin/backup-database.sh
0 3 * * * /usr/local/bin/backup-files.sh
```

## Deployment Process

### 1. Pre-deployment Checklist
- [ ] Update application code
- [ ] Run tests locally
- [ ] Update dependencies
- [ ] Check environment variables
- [ ] Backup current version

### 2. Deployment Steps
```bash
# 1. Pull latest code
cd /var/www/ezytix4u
git pull origin main

# 2. Install dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run production

# 3. Run migrations
php artisan migrate --force

# 4. Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 5. Restart services
sudo systemctl restart php8.1-fpm
sudo systemctl restart nginx
```

### 3. Post-deployment Verification
- [ ] Check application logs
- [ ] Verify payment gateways
- [ ] Test user registration
- [ ] Test ticket booking
- [ ] Check email functionality

## Maintenance Procedures

### 1. Regular Maintenance
```bash
# Weekly tasks
php artisan queue:restart
php artisan schedule:run

# Monthly tasks
composer update --no-dev
npm update
```

### 2. Security Updates
```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Update PHP packages
composer update --no-dev
```

### 3. Performance Monitoring
```bash
# Check server resources
htop
df -h
free -h

# Check application performance
php artisan queue:work --timeout=60
```

## Troubleshooting

### Common Issues

#### 1. 500 Internal Server Error
```bash
# Check PHP-FPM logs
sudo tail -f /var/log/php8.1-fpm.log

# Check Nginx logs
sudo tail -f /var/log/nginx/error.log
```

#### 2. Database Connection Issues
```bash
# Test database connection
mysql -u ezytix4u_user -p ezytix4u

# Check MySQL status
sudo systemctl status mysql
```

#### 3. Payment Gateway Issues
```bash
# Check payment logs
tail -f /var/www/ezytix4u/storage/logs/laravel.log | grep -i payment
```

### Emergency Procedures

#### 1. Rollback Deployment
```bash
# Revert to previous version
git reset --hard HEAD~1
composer install --no-dev --optimize-autoloader
php artisan migrate:rollback
```

#### 2. Restore from Backup
```bash
# Restore database
mysql -u ezytix4u_user -p ezytix4u < /backups/database_YYYYMMDD_HHMMSS.sql

# Restore files
tar -xzf /backups/files_YYYYMMDD_HHMMSS.tar.gz -C /
```

## Support and Resources

### Documentation
- [Laravel Deployment Guide](https://laravel.com/docs/deployment)
- [Nginx Configuration](https://nginx.org/en/docs/)
- [MySQL Optimization](https://dev.mysql.com/doc/refman/8.0/en/optimization.html)

### Monitoring Tools
- **Application**: Laravel Telescope, Laravel Debugbar
- **Server**: htop, iotop, nethogs
- **Database**: MySQL Workbench, phpMyAdmin
- **Logs**: logwatch, fail2ban

### Contact Information
- **Technical Support**: support@ezytix4u.com
- **Emergency Contact**: +60-XX-XXXX-XXXX
- **Hosting Provider**: Your hosting provider support 