<?php

// start session
session_start();

// set some ini-options
ini_set('pcre.backtrack_limit', 999999999);
ini_set('pcre.recursion_limit', 999999999);

// set a default timezone if no one was set by PHP.ini
if(ini_get('date.timezone') == '') date_default_timezone_set('Europe/Brussels');

// require the installer class
require_once 'engine/installer.php';

// run instance
new Installer();

?>