# SQLSnipeIT Installation Guide

**Version:** Tested on Snipe-IT v8.2.1 
**Platform:** Windows 11 with SQL Server 2019  
**Status:** Testing phase - use at your own risk

## Overview

SQLSnipeIT is a modified version of Snipe-IT asset management software that runs on Microsoft SQL Server 2019 instead of MySQL. This guide covers installation on Windows 11.



Functionality Status
✅ Confirmed Working Features

API: Full REST API functionality operational
GUI: Web interface displays and functions correctly
Navigation: All menu systems and page routing work
Changes/Audit Log: Asset change tracking functioning
Search: Search functionality across assets working
LDAP Integration: Active Directory/LDAP authentication confirmed working

❌ Known Non-Working Features

Backups: No SQL Server backup script exists

Original MySQL backup scripts incompatible
Custom SQL Server backup solution needs to be developed


## Prerequisites

Before starting, ensure you have:
- **PHP 8.3** with required extensions
- **Apache Web Server** (will be installed as a Windows service)
- **Composer** for PHP dependency management
- **SQL Server 2019** installed and configured
- **Administrator access** to your Windows system

## Installation Steps

### Step 1: Install SQL Server PHP Drivers

1. Download [Microsoft Drivers for PHP for SQL Server](https://docs.microsoft.com/en-us/sql/connect/php/download-drivers-php-sql-server) from Microsoft's official website
2. Extract the downloaded archive
3. Copy the appropriate driver files to your PHP extensions directory:
   - `php_sqlsrv.dll` → `C:\php\ext`
   - `php_pdo_sqlsrv.dll` → `C:\php\ext`

### Step 2: Install and Configure PHP

1. **Download PHP 8.3**
   - Get PHP 8.3 from [php.net](https://www.php.net)
   - Extract to `C:\php`

2. **Add PHP to System PATH**
   - Open System Properties → Environment Variables
   - Add `C:\php` to the PATH variable

3. **Configure PHP (php.ini)**
   - Open `C:\php\php.ini`
   - Add these SQL Server extensions (adjust version numbers as needed):
     ```ini
     extension=php_sqlsrv_82_ts_x64.dll
     extension=php_pdo_sqlsrv_82_ts_x64.dll
     ```
   - Enable these required extensions:
     - mbstring
     - openssl
     - pdo
     - tokenizer
     - xml
     - ctype
     - json
     - bcmath

### Step 3: Install Apache Web Server (You can skip to step 6 if already configured using another service)

1. Download Apache from [Apache Lounge](https://www.apachelounge.com/download/)
2. Install Apache as a Windows service following the installer instructions
3. **Important:** Stop Apache service after installation for configuration

### Step 4: Configure Apache for PHP

1. **Edit Apache Configuration** (`C:\Apache24\conf\httpd.conf`)
   
2. **Add PHP Module Configuration:**
   ```apache
   LoadModule php_module "C:/php/php8apache2_4.dll"
   AddHandler application/x-httpd-php .php
   PHPIniDir "C:/php"
   ```

3. **Set Directory Index:**
   ```apache
   <IfModule dir_module>
       DirectoryIndex index.php index.html
   </IfModule>
   ```

### Step 5: Enable Required Apache Modules

1. **Enable Virtual Hosts**
   - In `httpd.conf`, uncomment:
     ```apache
     Include conf/extra/httpd-vhosts.conf
     ```

2. **Enable mod_rewrite**
   - In `httpd.conf`, uncomment:
     ```apache
     LoadModule rewrite_module modules/mod_rewrite.so
     ```

3. **Configure Virtual Host** (`C:\Apache24\conf\extra\httpd-vhosts.conf`)
   ```apache
   <VirtualHost *:80>
       DocumentRoot "C:/Apache24/htdocs/snipe-it/public"
       ServerName hsaws113964
   
       <Directory "C:/Apache24/htdocs/snipe-it/public">
           Options Indexes FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

### Step 6: Install Snipe-IT

1. **Download Snipe-IT**
   - Download from [GitHub](https://github.com/snipe/snipe-it)
   - **Note:** Use the modified version for MS SQL support, not Git clone
   - Extract to `C:\Apache24\htdocs\snipe-it`

2. **Configure Environment File**
   - Rename `.env.example` to `.env`
   - Edit `.env` with these settings:
   
   ```env
   # Application Settings
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=base64:
   APP_URL=http://hostname
   APP_TIMEZONE='America/Los_Angeles'
   APP_LOCALE='en-US'
   MAX_RESULTS=500
   
   # Database Settings
   DB_CONNECTION=sqlsrv
   DB_HOST=DBServer
   DB_PORT=1433
   DB_DATABASE=Database;TrustServerCertificate=true
   DB_USERNAME=[your_username]
   DB_PASSWORD=[your_password]
   DB_PREFIX=null
   DB_DUMP_PATH=''
   DB_CHARSET=utf8mb4
   DB_COLLATION=utf8mb4_unicode_ci
   ```

3. **Replace Migration Files** (if provided with modified versions for SQL Server)

### Step 7: Install Composer

1. Download [Composer](https://getcomposer.org/download/)
2. Place Composer in `C:\Apache24\htdocs\snipe-it`
3. Run the installer and follow instructions

### Step 8: Install Dependencies

Open Command Prompt as Administrator and run:

```cmd
cd C:\Apache24\htdocs\snipe-it
composer install --no-dev --prefer-source
```

### Step 9: Generate Application Key

```cmd
php artisan key:generate
```

### Step 10: Start Services

1. Start Apache service
2. Verify it's running in Windows Services

### Step 11: Access Snipe-IT

Open your browser and navigate to:
```
http://hsaws113964
```

## Post-Installation Database Fix

After installation, run this SQL command on your SQL Server to fix the theme query issue:

```sql
ALTER TABLE settings ALTER COLUMN skin VARCHAR(191);
```

## Troubleshooting Tips

- **Apache won't start:** Check error logs in `C:\Apache24\logs\error.log`
- **PHP errors:** Verify all required extensions are enabled in `php.ini`
- **Database connection fails:** Ensure SQL Server authentication is configured and credentials are correct
- **Page not found:** Verify virtual host configuration and that mod_rewrite is enabled

## Important Notes

⚠️ **Testing Phase:** This configuration is still being tested. While core features appear functional, bugs may exist.

⚠️ **Windows Only:** This guide is specific to Windows 11. Linux compatibility is unconfirmed.

⚠️ **Modified Version:** This requires a specially modified version of Snipe-IT to work with MS SQL Server.

## Support

For issues specific to:
- **Snipe-IT:** Check the [official documentation](https://snipe-it.readme.io/)
- **SQL Server connectivity:** Review Microsoft's PHP driver documentation
- **Apache/PHP:** Consult respective official documentation
