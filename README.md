Attogram Framework
==================
[//]: # ( Attogram Framework - README.md (markdown) - v0.1.3 )

[![Build Status](https://travis-ci.org/attogram/attogram.svg?branch=master)](https://travis-ci.org/attogram/attogram)
[![Latest Stable Version](https://poser.pugx.org/attogram/attogram-framework/v/stable)](https://packagist.org/packages/attogram/attogram-framework)
[![Latest Unstable Version](https://poser.pugx.org/attogram/attogram-framework/v/unstable)](https://packagist.org/packages/attogram/attogram-framework)
[![Total Downloads](https://poser.pugx.org/attogram/attogram-framework/downloads)](https://packagist.org/packages/attogram/attogram-framework)
[![License](https://poser.pugx.org/attogram/attogram-framework/license)](https://github.com/attogram/attogram/blob/master/LICENSE.md)
[`[CHANGELOG]`](https://github.com/attogram/attogram/blob/master/CHANGELOG.md)
[`[TODO]`](https://github.com/attogram/attogram/blob/master/TODO.md)

* The Attogram Framework provides developers a PHP skeleton starter site with
content modules, file-based URL routing, IP-protected backend, user system,
integrated SQLite database with web admin, Markdown parser, jQuery and Bootstrap.

* The Attogram Framework is Dual Licensed under the MIT License (MIT)
_or_ the GNU General Public License version 3 or higher (GPL-3.0+), at your choosing.

More Info
=========

* Attogram @ GitHub: https://github.com/attogram/attogram

* [Download latest Attogram as a zip file](https://github.com/attogram/attogram/archive/master.zip)

* Attogram @ Packagist: https://packagist.org/packages/attogram/attogram-framework

Notes
=====
* Need the `vendor` directory and can't run composer?  Download and install the [Attogram vendor package as a zip file](https://github.com/attogram/attogram-vendor/archive/master.zip).

* Or get the Attogram vendor package @ GitHub: https://github.com/attogram/attogram-vendor


_Attogram now has modules!  Updated docs coming soon..._

Attogram gives developers a jumpstart to quickly create web sites.

Attogram is a PHP-based framework that provides developers a skeleton site with:

* file-based URL routing
* IP-protected backend
* simple user system
* integrated SQLite database with phpLiteAdmin
* Markdown parser
* jQuery and Bootstrap

After that, Attogram tries to stay out of your way while you do your thing!

Attogram is Dual Licensed under the The MIT License *or* the GNU General Public License, at your choosing.

Requirements
============
* PHP5 or PHP7, with SQLite PDO driver
* Apache 2.2.16 or higher, with setting: AllowOveride all

Setup: Install
==============

* Get Attogram:
  * use Composer:  
    `composer create-project attogram/attogram-framework your-install-directory`
  * or manually install:
    * Download latest code from GitHub:[`https://github.com/attogram/attogram/archive/master.zip`](https://github.com/attogram/attogram/archive/master.zip)
    * If composer is not available, also download the vendor distribution from [`https://github.com/attogram/attogram-vendor/archive/master.zip`](https://github.com/attogram/attogram-vendor/archive/master.zip) and move the `./vendor/` directory to the top level of your install directory.

* Setup your web server to use the `./public/` directory as the web site root.

* edit `./public/.htaccess`, set **FallbackResource**, **ErrorDocument 403** and **ErrorDocument 404** to the full web path to the index.php file in the install directory.

* (optional) copy `./public/config.sample.php` to `./public/config.php` and edit to change default settings .

Setup: Admin
============
* admin pages are IP protected
* change the allowed admin IPs by setting `$config['admins']` in `./public/config.php`
* default admin IPs is localhost in ip4 and ip6: `array( '127.0.0.1', '::1' )`
* admin page requests from non-admin IPs will result in a 404 Page Not Found error

Setup: Database
===============
* Make sure the database file `./db/global` is writeable by the web server
* Tables are lazily created when needed.  To create all tables at once,
goto the [**db-setup admin page**](../db-setup/) and click **Create Attogram Tables**
* phpLiteAdmin is available for database administration, goto the [**db-admin admin page**](../db-admin/), default password is **attogram**

Setup: Users
============
* load the homepage, goto admin action [**users**](../users/), click **Create New User**
* enter username, password, etc. and click **Insert**
* load the homepage, click [**login**](../login), login as the new user

Create a page
=============
* create a new **PHP** or **Markdown** file in the `./actions/` directory, add anything you want!
* The filename is used as the page URL.  ./actions/**example**.php = example.com/**example**/

PHP pages
=========
* PHP filenames must end in `.php`
* The Attogram object is available via the `$this` variable
* Helpful functions:
  * $this->page_header($title)
  * $this->page_footer()
  * $this->log->debug(), ->error(), etc.
  * $this->get_site_url()
  * $this->error404($error_message)
  * $this->is_admin()
  * $this->is_logged_in()
* Depth settings in `./public/config.php`
  * `$config['depth']['insert-action-name-here']`
* End Slash settings in `./public/config.php`
  * `$config['no_end_slash'][] = 'insert-action-name-here'`

Markdown pages
==============
* Markdown filenames must end in `.md`
* The first line of the Markdown file is used as the page title

Remove a page
=============
* delete the pages corresponding file from the `./actions/` directory

Admin pages
===========
* create/delete the same as normal pages, but in the `./admin/` directory

Database tables
===============
* To add a table, add a file into `./tables/` directory
* The filename must be the name of the table
* File content is the sql `CREATE TABLE ...` statement
* tables are automatically created upon first use

Web discovery
=============
* If [`./robots.txt`](../robots.txt) does not exist, Attogram dynamically serves it, with a link to the Sitemap
* If [`./sitemap.xml`](../sitemap.xml) does not exist, Attogram dynamically serves it, with a listing of all public pages

Admin URL overrides
===================
* admins may use URL/[`?noadmin`](?noadmin) on any page to turn off admin access
* admins may use URL/[`?debug`](?debug) on any page to turn on debugging

More Info
=========
* Attogram @ GitHub: https://github.com/attogram/attogram
* Attogram vendor package @ GitHub: https://github.com/attogram/attogram-vendor
* Attogram @ Packagist: https://packagist.org/packages/attogram/attogram-framework
* Demo: http://getitdaily.com/attogram/

Attogram Modules
===
[//]: # ( Attogram Framework - Modules Readme (markdown) - v0.1.14 )

* Easily create modules to extend Attogram!

* Each module lives in its own subdirectory within the Attogram `./modules/` directory.

# Module Structure

* Modules may have the following subdirectories:

 * `actions/` - The Public Actions (.php or .md markdown files)  If present, the `home` action is used as the home page.

 * `admin_actions/` - The backend admin-only Actions (.php or .md markdown files)

 * `configs/`- Configurations via `namespace Attogram; global $config; $config['configname'] = ...`, loaded at startup (.php files only)

 * `includes/` - Included files, loaded at startup (.php files only)

 * `tables/` - Database table definitions. Tables are created lazily as needed (.sql files only)

 * `templates/` - Templates, to override default Attogram templates  (.php files only)

 * `public/` - Files for public consumption. Served via requests to the `web/` virtual web directory

* Modules are loaded in _directory list order_.
 * Actions and files _cascade_: a duplicate action name or file will overwrite any previous modules action or file of the same name.

# Standard Modules

Standard modules included with the Attogram Framework distribution:

## Module: \_attogram
* `./modules/_attogram/` - Default Attogram info and files
* `./modules/_attogram/actions/about.md` - Public Action: About page
* `./modules/_attogram/actions/home.php` - Public Action: Home page
* `./modules/_attogram/actions/license.php` - Public Action: License page
* `./modules/_attogram/admin_actions/info.php` - Admin Action: Attogram Info page
* `./modules/_attogram/admin_actions/events.php` - Admin Action: Event log view page
* `./modules/_attogram/public/attogram.css` - Virtual Web Directory: attogram global CSS
* `./modules/_attogram/public/jquery.min.js` - Virtual Web Directory: jQuery library
* `./modules/_attogram/public/bootstrap/css/bootstrap.min.css` - Virtual Web Directory: Bootstrap library CSS
* `./modules/_attogram/public/bootstrap/fonts/glyphicons-halflings-regular.[eot,svg,ttf,woff,woff2]` - Virtual Web Directory: Bootstrap glyph font
* `./modules/_attogram/public/bootstrap/js/bootstrap.min.js` - Virtual Web Directory: Bootstrap library javascript
* `./modules/_attogram/tables/event.sql` - Event lo database table definition

## Module: \_database
* `./modules/_database/` - SQLite database for Attogram
* `./modules/_database/admin_actions/db-admin.php` - Admin Action: Database web admin via phpLiteAdmin
* `./modules/_database/admin_actions/db-tables.php` - Admin Action: Database table information
* `./modules/_database/includes/attogram_database.php` - Attogram Database Interface
* `./modules/_database/includes/sqlite_database.php` - Attogram SQLite helper class

## Module: \_user
* `./modules/_user/` - Simple user system for Attogram. (requires `database` module)
* `./modules/_user/actions/login.php` - Public Action: Login page
* `./modules/_user/actions/user.php` - Public Action: User page
* `./modules/_user/admin_actions/user-admin.php` - Admin Action: Users editor
* `./modules/_user/includes/attogram_user.php` - Attogram User helper class
* `./modules/_user/tables/user.sql` - user database table definition

# Misc

* More example modules can be found in the `./xtra/` directory. Modules in that directory will not be loaded by Attogram.
