<?php

// require init
require_once 'init.php';

// define if needed
if(!defined('APPLICATION')) define('APPLICATION', 'api');

// initialize components
new APIInit(APPLICATION);

// create backend
new API();

?>