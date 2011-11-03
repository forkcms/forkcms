<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

// if someone tries to access the folder frontend, we redirect them to the homepage
if(!defined('APPLICATION'))
{
	header('Location: /');
	exit;
}

require_once 'init.php';

new FrontendInit(APPLICATION);
new Frontend();
