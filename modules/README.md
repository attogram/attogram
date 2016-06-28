Attogram Modules
===
[//]: # ( Attogram Framework - Modules Readme (markdown) - v0.1.12 )

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

* Modules are loaded in directory list order.
 * Action names cascade: a duplicate action name will overwrite any previous modules action of the same name.

# Standard Modules

Standard modules included with the Attogram Framework distribution:

## Module: Attogram
* `./modules/attogram/` - Default site with information about Attogram
* `./modules/attogram/actions/home.php` - Public Action: Home page
* `./modules/attogram/actions/about.md` - Public Action: About page
* `./modules/attogram/actions/license.php` - Public Action: License page
* `./modules/attogram/admin_actions/info.php` - Admin Action: Attogram Info page

## Module: Database
* `./modules/database/` - SQLite database for Attogram
* `./modules/database/admin_actions/db-admin.php` - Admin Action: Database web admin via phpLiteAdmin
* `./modules/database/admin_actions/db-setup.php` - Admin Action: Database table setup
* `./modules/database/includes/attogram_database.php` - Attogram Database Interface
* `./modules/database/includes/sqlite_database.php` - Attogram SQLite helper class

## Module: User
* `./modules/user/` - Simple user system for Attogram. (requires `database` module)
* `./modules/user/actions/login.php` - Public Action: Login page
* `./modules/user/actions/user.php` - Public Action: User page
* `./modules/user/admin_actions/user-admin.php` - Admin Action: Users editor
* `./modules/user/includes/attogram_user.php` - Attogram User helper class
* `./modules/user/tables/user.sql` - user database table definition

# Misc

* More example modules can be found in the `./contrib/` directory. Modules in this directory will not be loaded by Attogram.
