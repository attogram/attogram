<?php
// Attogram - action - documentation

$title = 'Attogram - Documentation';
include('templates/header.php');

?>
<div class="body">
<pre>
Attogram <?php print $this->version; ?> Documentation:

Setup:

* Fork <a target="code" href="https://github.com/attogram/attogram">Attogram on GitHub</a>: https://github.com/attogram/attogram.git
* or Download zip: <a href="https://github.com/attogram/attogram/archive/master.zip">https://github.com/attogram/attogram/archive/master.zip</a>
* install code in web accessible area
* edit ./.htaccess - set correct path name for ErrorDocument 403 and 404, FallBackResource
* copy ./libs/config.sample.cphp to config.php and add your admin ip
* chmod 777 ./db/ 
* chmod 777 ./db/global

File structure:

.
|-- .htaccess - sets index.php keyhole, 403 and 404 errors
|-- <a href="../">index.php</a> - This is Attogram.  Attogram controls everything.
|-- config.php - base URL setting, admin IP whitelist
|-- <a href="../404.php">404.php</a> - Error 404 Page Not Found 
|-- <a href="../css.css">css.css</a> - global CSS, included in templates/header.php
|-- actions - the 'pages' 
|   |-- .htaccess - deny all web access to this directory
|   |-- <a href="../admin/">admin.php</a> - admin backend homepage
|   |-- <a href="../admin-check/">admin-check.php</a> - admin system check
|   |-- <a href="../admin-db/">admin-db.php</a> - web-based SQLite database management via phpLiteAdmin v1.9.8
|   |-- <a href="../admin-messages/">admin-messages.php</a> - view/edit/delete messages from the contact form
|   |-- <a href="../">home.php</a> - homepage
|   |-- <a href="../contact/">contact.php</a> - contact form, saves messages to database 
|   |-- <a href="../documentation/">documentation.php</a> - sub-page
|   `-- <a href="../license/">license.php</a> - sub-page
|-- db - the database(s) directory
|   |-- .htaccess - deny all web access to this directory
|   `-- global - the global SQLite database
|-- libs - support code
|   |-- .htaccess - deny all web access to this directory
|   `-- check.sh - unix shell script for system check
|-- plugins - attogram plugins
|   |-- .htaccess - deny all web access to this directory
|   |-- error.php - error reporting plugin 
|   |-- test.php - echo test plugin 
|   `-- timer.php - show attogram completion time in attoseconds
`-- templates - global templates
    |-- .htaccess - deny all web access to this directory
    |-- footer.php - global HTML footer
    `-- header.php - global HTML header

Attogram object:

function __construct() - run attogram
function load_config() - load config file
function route() - decide what to action to do
function action() - load an action
function error404() - load ./404.php
function hook( $hook, $return=false ) - call a plugin hook
function get_plugins() - get array of active plugin objects
function get_actions() - get array of action names
function is_admin() - is admin? based on admin IP whitelist in ./libs/config.php
function query( $sql, $bind=array() ) - database query, return results as array
function queryb( $sql, $bind=array() ) - database query, return boolean true/false
function get_db() - get the PDO database object

Plugins:

* class name is plugin_{filename}
* function is_active() returns true/false
* function __construct() gets passed in the $attogram object
* function hook() gets called for every hook

Hooks:

PRE-INIT,   POST-INIT
PRE-CONFIG, POST-CONFIG
PRE-ACTION, POST-ACTION
PRE-404,    POST-404
PRE-QUERY,  POST-QUERY, ERROR_QUERY
PRE-DB,     POST_DB,    ERROR_DB
PRE-HEADER, POST-HEADER
PRE-FOOTER, POST-FOOTER
PRE-HOME,   POST-HOME
PRE-ADMIN,  POST-ADMIN
PRE-PHPLITEADMIN
</pre>
</div>
<?php
include('templates/footer.php');

