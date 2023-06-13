# SQLSnipeIT
Tested Version Snipe-It 6.1.0

## Warning this is still in testing, while all features seem to be working. There could still be bugs present so use at your own risk.

This is a working version of Snipe-IT on microsofts SQL Server 2019.
This install was done on windows 10, not sure if it would work on linux.

Install Prerequisites
Install microsofts SQL ODBC drivers on host computer
https://learn.microsoft.com/en-us/sql/connect/odbc/download-odbc-driver-for-sql-server?view=sql-server-ver16

Install the Microsoft Drivers 5.11 for PHP for SQL Server Released
https://learn.microsoft.com/en-us/sql/connect/php/download-drivers-php-sql-server?view=sql-server-ver16
Extract the files and place them into the PHP Extensions folder.
Add the appropiate extensions to your PHP file.

Example of mine:
extension=php_sqlsrv_80_ts_x86.dll
extension=php_pdo_sqlsrv_80_ts_x86.dll
extension=php_sqlsrv_80_ts_x64.dll
extension=php_pdo_sqlsrv_80_ts_x64.dll
extension=php_sqlsrv_81_ts_x86.dll
extension=php_pdo_sqlsrv_81_ts_x86.dll
extension=php_sqlsrv_81_ts_x64.dll
extension=php_pdo_sqlsrv_81_ts_x64.dll
extension=php_sqlsrv_82_ts_x86.dll
extension=php_pdo_sqlsrv_82_ts_x86.dll
extension=php_sqlsrv_82_ts_x64.dll
extension=php_pdo_sqlsrv_82_ts_x64.dll
Restart the web server to apply the changes

Edit the .env file with the Microsft SQL Server details, and add the ";TrustServerCertificate=true"

DB_CONNECTION=sqlsrv
DB_DATABASE=mydbname;TrustServerCertificate=true

Finally copy the modified files to snipe-it\database\migrations
replace the existing files.
