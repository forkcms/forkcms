<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

// Fork has not yet been installed
$installer = dirname(__FILE__) . '/install/cache';
if(file_exists($installer) && is_dir($installer) && !file_exists($installer . '/installed.txt'))
{
	header('Location: /install');
}

use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/app/AppKernel.php';

$kernel = new AppKernel();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
