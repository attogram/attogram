Attogram Modules
===
[//]: # ( Attogram Framework - Modules Readme (markdown) - v0.0.3 )

* Attogram can be easily extended by creating you own modules

* Each module lives in its own subdirectory within the Attogram `./modules/` directory.

# Module Structure

* Modules may have the following subdirectories:

 * `actions/` - The Public Actions (php or markdown files)

 * `admin_actions/` - The Admin-only Actions (php or markdown files)

 * `configs/`- Configurations via `$config['configname'] = ...`, loaded at startup (php files only)

 * `includes/` - Includes, loaded at startup (php files only)

 * `tables/` - Database table definitions, lazily loaded as needed (sql files only)

* Modules are loaded in directory list order.
 * Action names cascade: a duplicate action name will overwrite any previous modules action of the same name.

# Standard Modules

Standard modules included with the Attogram Framework distribution:

## Module: Attogram
* `./modules/attogram/`
* Default site with information about Attogram
* Actions: home page, about page, license page
* Admin Actions: Attogram Info page

## Module: Database
* `./modules/database/`
* A SQLite database for Attogram
* Admin actions: DB admin, DB setup
* helper functions: pager() pagination, tabler() table helper

## Module: User
* `./modules/user/`
* Simple user system for Attogram
* Actions: login page, user page
* Admin Actions: user editor
* requires `database` module

# Misc

* `./modules_disabled/` is a holding area for disabled modules.  Modules in this directory will not be loaded by Attogram.
