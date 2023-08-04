<?php

use ForkCMS\App\AppKernel;

require_once __DIR__ . '/../autoload.php';
$kernel = new AppKernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();
return $kernel->getContainer()->get('doctrine')->getManager();
