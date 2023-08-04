<?php

use ForkCMS\Core\Domain\Kernel\Kernel;
use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__ . '/../../bootstrap.php';
(new Dotenv())->bootEnv(__DIR__ . '/../../../.env');

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();

return $kernel->getContainer()->get('doctrine')->getManager();
