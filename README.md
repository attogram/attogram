# Attogram Framework

[//]: # ( Attogram Framework - README.md (markdown) - v0.1.15 )

[![Build Status](https://travis-ci.org/attogram/attogram.svg?branch=master)](https://travis-ci.org/attogram/attogram)
[![Latest Stable Version](https://poser.pugx.org/attogram/attogram-framework/v/stable)](https://packagist.org/packages/attogram/attogram-framework)
[![Latest Unstable Version](https://poser.pugx.org/attogram/attogram-framework/v/unstable)](https://packagist.org/packages/attogram/attogram-framework)
[![Total Downloads](https://poser.pugx.org/attogram/attogram-framework/downloads)](https://packagist.org/packages/attogram/attogram-framework)
[![License](https://poser.pugx.org/attogram/attogram-framework/license)](https://github.com/attogram/attogram/blob/master/LICENSE.md)
[![Code Climate](https://codeclimate.com/github/attogram/attogram/badges/gpa.svg)](https://codeclimate.com/github/attogram/attogram)
[![Issue Count](https://codeclimate.com/github/attogram/attogram/badges/issue_count.svg)](https://codeclimate.com/github/attogram/attogram)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/0a50344b228f46c98ffb06b78b99cbe0)](https://www.codacy.com/app/attogram-project/attogram?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=attogram/attogram&amp;utm_campaign=Badge_Grade)
[`[CHANGELOG]`](https://github.com/attogram/attogram/blob/master/CHANGELOG.md)
[`[TODO]`](https://github.com/attogram/attogram/blob/master/TODO.md)

The Attogram Framework provides developers a PHP skeleton starter site with
a content module system, file-based URL routing, IP-protected backend,
Markdown parser, jQuery and Bootstrap.  

[Core modules](https://github.com/attogram/attogram/blob/master/modules/README.md)
available to add a [SQLite database](https://github.com/attogram/attogram-database)
with web admin, [user system](https://github.com/attogram/attogram-user), and more.

After that, Attogram tries to stay out of your way while you do your thing!

## More Info

* Attogram Framework @ GitHub: https://github.com/attogram/attogram
* Attogram Framework @ Packagist: https://packagist.org/packages/attogram/attogram-framework
* Attogram Framework @ Travis CI: https://travis-ci.org/attogram/attogram
* Attogram Framework vendor package @ GitHub: [(download ZIP)](https://github.com/attogram/attogram-vendor/archive/master.zip) https://github.com/attogram/attogram-vendor

## Requirements

* PHP 5.3.3 or higher, or PHP7
* PHP installed with SQLite PDO driver
* Apache 2.2.16 or higher, with setting: AllowOveride all

## Setup: Install

* Get Attogram:

  * use Composer:
    `composer create-project attogram/attogram-framework your-install-directory`
  * or manually install:

    * Download latest code from GitHub:
      [`https://github.com/attogram/attogram/archive/master.zip`](https://github.com/attogram/attogram/archive/master.zip)
    * If composer is not available, also download the vendor distribution from
      [`https://github.com/attogram/attogram-vendor/archive/master.zip`](https://github.com/attogram/attogram-vendor/archive/master.zip)
      and move the `./vendor/` directory to the top level of your install directory.

* Setup your web server to use the `./public/` directory as the web site root.
* edit `./public/.htaccess`, set **FallbackResource**, **ErrorDocument 403**
  and **ErrorDocument 404** to the full web path to the index.php file in
  the install directory.
* (optional) copy `./public/config.sample.php` to `./public/config.php` and
  edit to change default settings .

## Setup: Admin

* admin pages are IP protected
* change the allowed admin IPs by setting `$config['admins']` in
  `./public/config.php`
* default admin IPs is localhost in ip4 and ip6: `array( '127.0.0.1', '::1' )`
* admin page requests from non-admin IPs will result in a
  404 Page Not Found error

_Attogram now has modules!  Updated docs coming soon..._

## Setup: Database

* Install the Attogram Database Module
* Make sure the database file `./db/global` is writeable by the web server
* Tables are lazily created when needed.  
* To create all tables at once, goto the
  [**db-tables admin page**](../db-tables/) and click **Create Attogram Tables**
* phpLiteAdmin is available for database administration, goto the
  [**db-admin admin page**](../db-admin/), default password is **attogram**

## Setup: Users

* load the homepage, goto admin action [**users**](../users/),
  click **Create New User**
* enter username, password, etc. and click **Insert**
* load the homepage, click [**login**](../login), login as the new user

## Attogram Modules

* Easily create modules to extend Attogram!
* Each module lives in its own subdirectory within the Attogram
  `./modules/` directory.

### Module Structure

* Modules may have the following subdirectories:

  * `actions/` - The Public Actions (.php or .md markdown files)  If present,
    the `home` action is used as the home page.
  * `admin_actions/` - The backend admin-only Actions
    (.php or .md markdown files)
  * `configs/`- Configurations via
    `namespace Attogram; global $config; $config['configname'] = ...`,
    loaded at startup (.php files only)
  * `includes/` - Included files, loaded at startup (.php files only)
  * `tables/` - Database table definitions. Tables are created lazily as needed
    (.sql files only)
  * `templates/` - Templates, to override default Attogram templates  
    (.php files only)
  * `public/` - Files for public consumption. Served via requests to the
    `web/` virtual web directory

* Modules are loaded in _directory list order_.

  * Actions and files _cascade_: a duplicate action name or file will overwrite
    any previous modules action or file of the same name.

### Core Modules

* Attogram Database - <https://github.com/attogram/attogram-database>
* Attogram User - <https://github.com/attogram/attogram-user>
* Attogram Info - <https://github.com/attogram/attogram-info>
* Attogram Contact Form - <https://github.com/attogram/attogram-contactform>

## Old Docs

## Create a page

* create a new **PHP** or **Markdown** file in the `./actions/` directory, add anything you want!
* The filename is used as the page URL.  ./actions/**example**.php = example.com/**example**/

## PHP pages

* PHP filenames must end in `.php`
* The Attogram object is available via the `$this` variable
* Helpful functions:

  * $this->pageHeader($title)
  * $this->pageFooter()
  * $this->log->debug(), ->error(), etc.
  * $this->getSiteUrl()
  * $this->error404($error_message)
  * $this->isAdmin()
  * $this->isLoggedIn()

* Depth settings in `./public/config.php`

  * `$config['depth']['insert-action-name-here']`

* End Slash settings in `./public/config.php`

  * `$config['noEndSlash'][] = 'insert-action-name-here'`

## Markdown pages

* Markdown filenames must end in `.md`
* The first line of the Markdown file is used as the page title

## Remove a page

* delete the pages corresponding file from the `./actions/` directory

## Admin pages

* create/delete the same as normal pages, but in the `./admin_actions/` directory

## Database tables

* To add a table, add a file into `./tables/` directory
* The filename must be the name of the table
* File content is the sql `CREATE TABLE ...` statement
* tables are automatically created upon first use

## Web discovery

* If [`./robots.txt`](../robots.txt) does not exist, Attogram dynamically serves it, with a link to the Sitemap
* If [`./sitemap.xml`](../sitemap.xml) does not exist, Attogram dynamically serves it, with a listing of all public pages

## Admin URL overrides

* admins may use URL/[`?noadmin`](?noadmin) on any page to turn off admin access
* admins may use URL/[`?debug`](?debug) on any page to turn on debugging
