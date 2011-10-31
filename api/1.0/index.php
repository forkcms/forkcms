<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

require_once 'init.php';

// define if needed
if(!defined('APPLICATION'))
{
	define('APPLICATION', 'api');
}

// initialize components
new APIInit(APPLICATION);
new API();
