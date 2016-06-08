Attogram Modules
===
[//]: # ( Attogram Framework - Modules Readme (markdown) - v0.0.3 )

* Each module is in its own directory

* Modules may have the following subdirectories:

 * `actions/` - The Public Actions (php or markdown files)

 * `admin_actions/` - The Admin-only Actions (php or markdown files)

 * `configs/`- Configurations via `$config['configname'] = ...`, loaded at startup (php files only)

 * `includes/` - Includes, loaded at startup (php files only)

 * `tables/` - Database table definitions, lazily loaded as needed (sql files only)

* Modules are loaded in directory list order.
 * A duplicate action name will overwrite the previous modules action of the same name.
