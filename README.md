### Welcome to the *Sell Books Direct 1.9.7* Release!
## RELEASE INFORMATION

*Sell Books Direct*

### SYSTEM REQUIREMENTS

Sell Books Direct requires PHP 5.3.3 or later; we recommend using the latest
PHP version whenever possible.

### INSTALLATION

Sell Books Direct requires the Zend Framework 1.11.12 or later
library to be in your PHP `include_path` or placed directly or symlinked into
the `library` directory.e

Create the necessary database tables using the SQL files under `docs/sql/`. If
you are installing fresh, run the SQL files sequentially by version number from
`v1.0.0.sql` onwards. If you are upgrading an existing installation, run the SQL
files sequentially by version number starting with the version immediately after
your current version you are upgrading from.

You can create the initial admin user using `docs/sql/adminUser.sql`. The users
password is `123`. You can change this password once you are logged in for the
first time.

All application configuration is done in the `local.ini` file. This file will
not exist initially, so save a copy of `application/configs/local.ini.dist` to
`application/configs/local.ini`. The `local.ini.dist` file contains all possible
configurations.

An Apache2 virtual host needs to be configured to point to the `public`
directory. The following virtual host configuration is recommended:

    <VirtualHost *:80>
        ServerName sellbooksdirect.com
        SetEnv APPLICATION_ENV production
        DocumentRoot "/path/to/application/public/"
        <Directory "/path/to/application/public/">
            Order allow,deny
            Allow from all
            AllowOverride all
        </Directory>
    </VirtualHost>

### LICENSE

The files in this archive are Copyright (c) 2012 McKenzie Books, Inc. All rights
reserved (http://www.mckenzieservices.com/)
