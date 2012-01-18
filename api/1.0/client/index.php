<?php

require_once '../init.php';
require_once '../engine/api.php';
require_once '../engine/client.php';

// define if needed
if(!defined('APPLICATION'))
{
	define('APPLICATION', 'api');
}

// loads a new client
new APIInit(APPLICATION);
new APIClient();