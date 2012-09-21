<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Request;

// require
// @todo this is wrong. It will break our install validating.
require_once 'autoload.php';

$kernel = new AppKernel();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
