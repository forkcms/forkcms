<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

// require
// @todo this is wrong. It will break our install validating.
require_once 'library/globals.php';
require_once 'autoload.php';

// @todo we also need the autoloader of spoon before we start our application (so we can define services)
set_include_path('library' . PATH_SEPARATOR . get_include_path());
require_once 'spoon/spoon.php';

require_once 'bootstrap.php';
require_once 'routing.php';


// create new instance
$app = new ApplicationRouting();
