# Troubleshooting Guide

## Common Issues and Solutions

### Development Environment

#### 1. Composer Dependencies
**Issue**: Composer install fails
**Solutions**:
```bash
# Clear composer cache
composer clear-cache

# Remove vendor directory and composer.lock
rm -rf vendor composer.lock

# Reinstall dependencies
composer install
```

#### 2. Node.js Dependencies
**Issue**: npm install fails
**Solutions**:
```bash
# Clear npm cache
npm cache clean --force

# Remove node_modules and package-lock.json
rm -rf node_modules package-lock.json

# Reinstall dependencies
npm install
```

#### 3. Environment Configuration
**Issue**: .env file not working
**Solutions**:
```bash
# Copy example env file
cp .env.example .env

# Generate application key
php artisan key:generate

# Clear config cache
php artisan config:clear
```

### Database Issues

#### 1. Migration Errors
**Issue**: Migrations fail to run
**Solutions**:
```bash
# Reset database
php artisan migrate:fresh

# Run migrations with seed
php artisan migrate --seed

# Check migration status
php artisan migrate:status
```

#### 2. Connection Issues
**Issue**: Cannot connect to database
**Solutions**:
1. Check .env configuration:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

2. Verify MySQL service is running:
```bash
# For macOS
brew services list

# For Linux
sudo systemctl status mysql
```

### Application Issues

#### 1. 500 Server Error
**Solutions**:
1. Check Laravel logs:
```bash
tail -f storage/logs/laravel.log
```

2. Enable debug mode in .env:
```
APP_DEBUG=true
```

3. Clear application cache:
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

#### 2. API Authentication Issues
**Solutions**:
1. Check token generation:
```php
// Verify token generation in your code
$token = $user->createToken('auth-token')->plainTextToken;
```

2. Verify token in request:
```bash
# Check Authorization header
curl -H "Authorization: Bearer your-token" http://your-api/endpoint
```

3. Check token expiration in config/auth.php:
```php
'expires_in' => 60 * 24, // 24 hours
```

### Frontend Issues

#### 1. Vue.js Component Errors
**Solutions**:
1. Check browser console for errors
2. Verify component registration
3. Check props and data types
4. Use Vue DevTools for debugging

#### 2. Asset Compilation
**Issue**: Assets not compiling
**Solutions**:
```bash
# Clear node_modules
rm -rf node_modules

# Clear npm cache
npm cache clean --force

# Reinstall dependencies
npm install

# Rebuild assets
npm run dev
```

### Performance Issues

#### 1. Slow Page Load
**Solutions**:
1. Enable Laravel Debugbar:
```php
// config/debugbar.php
'enabled' => env('DEBUGBAR_ENABLED', true),
```

2. Check database queries:
```php
// Enable query logging
DB::enableQueryLog();
// Your code here
dd(DB::getQueryLog());
```

3. Optimize assets:
```bash
# Production build
npm run production

# Optimize images
npm run image-optimize
```

#### 2. Memory Issues
**Solutions**:
1. Increase PHP memory limit in php.ini:
```ini
memory_limit = 256M
```

2. Optimize composer autoload:
```bash
composer dump-autoload -o
```

### Deployment Issues

#### 1. Deployment Fails
**Solutions**:
1. Check deployment logs
2. Verify server requirements
3. Check file permissions:
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### 2. SSL Issues
**Solutions**:
1. Verify SSL certificate
2. Check .env configuration:
```
FORCE_HTTPS=true
```

3. Update Apache/Nginx configuration

### Security Issues

#### 1. CSRF Token Mismatch
**Solutions**:
1. Verify meta tag in layout:
```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

2. Include token in AJAX requests:
```javascript
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;
```

#### 2. File Upload Issues
**Solutions**:
1. Check file permissions
2. Verify upload limits in php.ini:
```ini
upload_max_filesize = 10M
post_max_size = 10M
```

3. Check storage configuration:
```php
// config/filesystems.php
'disks' => [
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
],
```

## Debug Tools

### Laravel
1. Laravel Debugbar
2. Laravel Telescope
3. Laravel Log Viewer

### Frontend
1. Vue DevTools
2. Chrome Developer Tools
3. Network Tab for API debugging

### Database
1. MySQL Workbench
2. Laravel Tinker
3. Database Management Tools

## Monitoring

### Application Monitoring
1. Laravel Telescope
2. New Relic
3. Sentry

### Server Monitoring
1. Server monitoring tools
2. Resource usage monitoring
3. Error log monitoring

## Getting Help

### Internal Resources
1. Project documentation
2. Team knowledge base
3. Code review guidelines

### External Resources
1. Laravel Documentation
2. Vue.js Documentation
3. Stack Overflow
4. GitHub Issues

## Best Practices

### Development
1. Use version control
2. Write tests
3. Follow coding standards
4. Document code
5. Regular backups

### Deployment
1. Use CI/CD
2. Test in staging
3. Monitor after deployment
4. Keep backups
5. Document changes 