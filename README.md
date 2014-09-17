Attogram PHP Framework
========

Attogram helps you quickly create web prototypes. Attogram is small, simple, and extendable.

Attogram quick specs:
* PHP5, Apache2, SQLite, Plugin architecture
* IP protected backend, web-based database management
* Open source, MIT/GPL dual license

Alpha preview available:
* Homepage: http://getitdaily.com/attogram/
* Documentation: http://getitdaily.com/attogram/documentation/
* License: http://getitdaily.com/attogram/license/
* Contact the Attogram Developers: http://getitdaily.com/attogram/contact/

Minimal Setup
=========

A simple setup with only the home page, and no plugins:

    .
    |-- .htaccess - apache setup for index.php keyhole, 403 and 404 errors
    |-- index.php - This is Attogram. Attogram controls everything.
    |-- 404.php - Error 404 Page Not Found
    `-- actions
        |-- .htaccess - apache setup to deny all web access to this directory
        `-- home.php - The Home Page
