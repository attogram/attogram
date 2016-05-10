Attogram PHP Framework
======================

Attogram helps you quickly create web prototypes.

Attogram is small, simple, and extendable.

Attogram quick specs:
* PHP5, Apache2, SQLite, Plugin architecture
* IP protected backend, web-based database management, simple user system
* Open source, MIT/GPL dual license, On GitHub: https://github.com/attogram/attogram

Requirements
============
* PHP5, with SQLite PDO driver
* Apache 2.2.16 or higher, with setting: AllowOveride all

Setup: Install
==============
* Install into a web accessable directory
* edit .htaccess, set FallbackResource to the full web path
  to the index.php file in the install directory
* copy config.sample.php to config.php
* edit config.php  to change default configuration settings

Setup: Database
==============
* load the homepage, goto admin action 'database-setup', click 'Create Attogram Tables'
* load the homepage, goto admin action 'database-phpLiteAdmin', login as 'attogram'

Setup: Users
============
* load the homepage, goto admin action 'users', click 'Create New User'
* enter username, password, etc. and click 'Insert'
* load the homepage, click 'login', login as the new user

Create a page
=============
* create a new php file in the ./actions/ directory, add anything you want!
* reload the homepage to see a link to the new page
* filenames must end in '.php'
* for admin pages, filenames must start with 'admin-'
* Attogram object is available via the $this variable

Remove a page
=============
* delete the pages corresponding php file from the ./actions/ directory

Make a new plugin
=================
* ... TBD ...

Programming with Attogram
=========================
* Attogram functions ... TBD ...
* Database functions ... TBD ...

More Info
=========
https://github.com/attogram/attogram
