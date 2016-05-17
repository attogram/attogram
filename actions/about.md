Attogram Framework
==================

Attogram gives developers a jumpstart to quickly create web sites.

Attogram runs on PHP with Apache. It provides developers a starting point with:

* auto file-based URL routing
* IP-protected backend
* simple user system
* integrated SQLite database with phpLiteAdmin
* Markdown parser
* jQuery and Bootstrap

After that, Attogram tries to stay out of your way while you do your thing!

Attogram is Dual Licensed under the The MIT License *or* the GNU General Public License, at your choosing.

Requirements
============
* PHP5, with SQLite PDO driver
* Apache 2.2.16 or higher, with setting: AllowOveride all

Setup: Install
==============
* Get Attogram:
 * Download latest master.zip from GitHub:  
   https://github.com/attogram/attogram/archive/master.zip
 * or use Composer:  
   composer create-project attogram/attogram-framework your-install-directory
* Install into a web accessible directory
* edit .htaccess, set FallbackResource to the full web path to the index.php file in the install directory
* (Optional) copy config.sample.php to config.php and edit to change default settings

Setup: Database
===============
* Make sure the database file ./db/global is writeable by the web server
* load the homepage, goto admin action 'db-setup', click 'Create Attogram Tables'
* load the homepage, goto admin action 'db-admin', login as 'attogram'

Setup: Users
============
* load the homepage, goto admin action 'users', click 'Create New User'
* enter username, password, etc. and click 'Insert'
* load the homepage, click 'login', login as the new user

Create a page
=============
* create a new PHP or Markdown file in the ./actions/ directory, add anything you want!
* reload the homepage to see a link to the new page
* PHP files must end in '.php', Markdown files must end in '.md'
* Attogram object is available via the $this variable

Remove a page
=============
* delete the pages corresponding file from the ./actions/ directory

Admin pages
===========
* create/delete the same as normal pages, but in the ./admin/ directory

More Info
=========
* Attogram @ GitHub: https://github.com/attogram/attogram
* Demo: http://getitdaily.com/attogram/