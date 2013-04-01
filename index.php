<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

// vendors not installed
if(!is_dir(__DIR__ . '/vendor'))
{
	echo 'You are missing some dependencies. Please run <code>composer install</code>.';
	exit;
}

// Fork has not yet been installed
$installer = dirname(__FILE__) . '/install/cache';
if(
	file_exists($installer) &&
	is_dir($installer) &&
	!file_exists($installer . '/installed.txt') &&
	substr($_SERVER['REQUEST_URI'], 0, 8) != '/install'
)
{
	header('Location: /install');
	exit;
}

use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/app/AppKernel.php';

$kernel = new AppKernel();

/**
 * @remark only for SumoCoders
 *
 * Here we define our Sumo class, which will set an error handler that pushes
 * to our Errbit-install.
 */
try {
	$debug = $kernel->getContainer()->getParameter('fork.debug');
	$errbitApiKey = $kernel->getContainer()->getParameter('sumo.errbit_api_key');

	// only activate the error handler when we aren't in debug-mode and an api key is provided
	if(!$debug && $errbitApiKey != '')
	{
		require_once __DIR__ . '/library/external/sumo.php';
		$sumo = new Sumo();
		$sumo->initErrbit($errbitApiKey);
	}
}
catch (\Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException $e)
{
	// do nothing
}

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
