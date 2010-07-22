<?php

// start session
session_start();

// require the installer class
require_once 'engine/installer.php';

// run instance
new Installer();

?>