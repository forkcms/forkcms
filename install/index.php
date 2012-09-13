<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

// start session
session_start();

// set a default timezone if no one was set by PHP.ini
if(ini_get('date.timezone') == '') date_default_timezone_set('Europe/Brussels');

// require the installer class
require_once 'engine/installer.php';

// we'll be using utf-8
define('SPOON_CHARSET', 'utf-8');
header('Content-type: text/html;charset=' . SPOON_CHARSET);

// run instance
new Installer();
