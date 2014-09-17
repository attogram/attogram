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

Maximum Setup
=========

A full setup with all available actions and plugins, database, and an admin interface:

    .
    |-- .htaccess - sets index.php keyhole, 403 and 404 errors
    |-- index.php - This is Attogram.  Attogram controls everything.
    |-- 404.php - Error 404 Page Not Found 
    |-- css.css - global CSS, included in templates/header.php
    |-- actions - the 'pages' 
    |   |-- .htaccess - deny all web access to this directory
    |   |-- admin.php - admin backend homepage
    |   |-- admin-check.php - admin system check
    |   |-- admin-db.php - web-based SQLite database management via phpLiteAdmin v1.9.8
    |   |-- admin-messages.php - view/edit/delete messages from the contact form
    |   |-- home.php - homepage
    |   |-- contact.php - contact form, saves messages to database 
    |   |-- documentation.php - sub-page
    |   `-- license.php - sub-page
    |-- db - the database(s) directory
    |   |-- .htaccess - deny all web access to this directory
    |   `-- global - the global SQLite database
    |-- libs - support code
    |   |-- .htaccess - deny all web access to this directory
    |   |-- check.sh - unix shell script for system check
    |   `-- config.php - base URL setting, admin IP whitelist
    |-- plugins - attogram plugins
    |   |-- .htaccess - deny all web access to this directory
    |   |-- error.php - error reporting plugin 
    |   |-- test.php - echo test plugin 
    |   `-- timer.php - show attogram completion time in attoseconds
    `-- templates - global templates
        |-- .htaccess - deny all web access to this directory
        |-- footer.php - global HTML footer
        `-- header.php - global HTML header

