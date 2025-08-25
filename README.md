SQLSnipeIT Installation Guide
Version: Tested on Snipe-IT v8.2.1
Platform: Windows 11 with SQL Server 2019
Status: Testing phase - use at your own risk
Overview
SQLSnipeIT is a modified version of Snipe-IT asset management software that runs on Microsoft SQL Server 2019 instead of MySQL. This guide covers installation on Windows 11.
Prerequisites
Before starting, ensure you have:

PHP 8.3 with required extensions
Apache Web Server (will be installed as a Windows service)
Composer for PHP dependency management
SQL Server 2019 installed and configured
Administrator access to your Windows system

Installation Steps
Step 1: Install SQL Server PHP Drivers

Download Microsoft Drivers for PHP for SQL Server from Microsoft's official website
Extract the downloaded archive
Copy the appropriate driver files to your PHP extensions directory:

php_sqlsrv.dll → C:\php\ext
php_pdo_sqlsrv.dll → C:\php\ext



Step 2: Install and Configure PHP

Download PHP 8.3

Get PHP 8.3 from php.net
Extract to C:\php


Add PHP to System PATH

Open System Properties → Environment Variables
Add C:\php to the PATH variable


Configure PHP (php.ini)

Open C:\php\php.ini
Add these SQL Server extensions (adjust version numbers as needed):
iniextension=php_sqlsrv_82_ts_x64.dll
extension=php_pdo_sqlsrv_82_ts_x64.dll

Enable these required extensions:

mbstring
openssl
pdo
tokenizer
xml
ctype
json
bcmath





Step 3: Install Apache Web Server (if not using apache skip to step 6)

Download Apache from Apache Lounge
Install Apache as a Windows service following the installer instructions
Important: Stop Apache service after installation for configuration

Step 4: Configure Apache for PHP 

Edit Apache Configuration (C:\Apache24\conf\httpd.conf)
Add PHP Module Configuration:
apacheLoadModule php_module "C:/php/php8apache2_4.dll"
AddHandler application/x-httpd-php .php
PHPIniDir "C:/php"

Set Directory Index:
apache<IfModule dir_module>
    DirectoryIndex index.php index.html
</IfModule>


Step 5: Enable Required Apache Modules

Enable Virtual Hosts

In httpd.conf, uncomment:
apacheInclude conf/extra/httpd-vhosts.conf



Enable mod_rewrite

In httpd.conf, uncomment:
apacheLoadModule rewrite_module modules/mod_rewrite.so



Configure Virtual Host (C:\Apache24\conf\extra\httpd-vhosts.conf)
apache<VirtualHost *:80>
    DocumentRoot "C:/Apache24/htdocs/snipe-it/public"
    ServerName hsaws113964

    <Directory "C:/Apache24/htdocs/snipe-it/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>


Step 6: Install Snipe-IT

Download Snipe-IT

Download from GitHub
Note: Use the modified version for MS SQL support, not Git clone
Extract to C:\Apache24\htdocs\snipe-it


Configure Environment File

Rename .env.example to .env
Edit .env with these settings:

env# Application Settings
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:
APP_URL=http://localhost
APP_TIMEZONE='America/Los_Angeles'
APP_LOCALE='en-US'
MAX_RESULTS=500

# Database Settings
DB_CONNECTION=sqlsrv
DB_HOST=Server
DB_PORT=1433
DB_DATABASE=HSAAssetManager;TrustServerCertificate=true
DB_USERNAME=[your_username]
DB_PASSWORD=[your_password]
DB_PREFIX=null
DB_DUMP_PATH=''
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci

Replace Migration Files (if provided with modified versions for SQL Server)

Step 7: Install Composer

Download Composer
Place Composer in C:\Apache24\htdocs\snipe-it
Run the installer and follow instructions

Step 8: Install Dependencies
Open Command Prompt as Administrator and run:
cmdcd C:\Apache24\htdocs\snipe-it
composer install --no-dev --prefer-source
Step 9: Generate Application Key
cmdphp artisan key:generate
Step 10: Start Services

Start Apache service
Verify it's running in Windows Services

Step 11: Access Snipe-IT
Open your browser and navigate to:
http://hsaws113964
Post-Installation Database Fix
After installation, run this SQL command on your SQL Server to fix the theme query issue:
sqlALTER TABLE settings ALTER COLUMN skin VARCHAR(191);
Troubleshooting Tips

Apache won't start: Check error logs in C:\Apache24\logs\error.log
PHP errors: Verify all required extensions are enabled in php.ini
Database connection fails: Ensure SQL Server authentication is configured and credentials are correct
Page not found: Verify virtual host configuration and that mod_rewrite is enabled

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


Possible Issues: Some less-used functions may have compatibility issues (testing ongoing)

⚠️ Features Under Testing

Advanced reporting functions
Bulk import operations
Email notifications
Custom field complex validations

Important Notes
⚠️ Testing Phase: This configuration is still being tested. While core features appear functional, bugs may exist.
⚠️ Windows Only: This guide is specific to Windows 11. Linux compatibility is unconfirmed.
⚠️ Modified Version: This requires a specially modified version of Snipe-IT to work with MS SQL Server.
⚠️ Backup Strategy Required: You'll need to implement your own SQL Server backup solution using SQL Server Agent jobs or PowerShell scripts.
Support
For issues specific to:

Snipe-IT: Check the official documentation
SQL Server connectivity: Review Microsoft's PHP driver documentation
Apache/PHP: Consult respective official documentation
