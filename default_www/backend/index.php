<?php

/** require init */
require_once 'init.php';

if(!defined('APPLICATION')) define('APPLICATION', 'backend');

// initialize components
$init = new Init(APPLICATION);

// create backend
$backend = new Backend();

?>