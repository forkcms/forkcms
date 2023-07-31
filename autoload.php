<?php

// @TODO rename this file to bootstrap.php to better reflect its function

use Symfony\Component\Dotenv\Dotenv;

// use vendor generated autoloader
$loader = require __DIR__ . '/vendor/autoload.php';

// Spoon is not autoloaded via Composer but uses its own old skool autoloader
set_include_path(__DIR__ . '/vendor/spoon/library' . PATH_SEPARATOR . get_include_path());
require_once 'spoon/spoon.php';

// load server variables
if (!array_key_exists('FORK_ENV', $_SERVER)) {
    $_SERVER['FORK_ENV'] = $_ENV['FORK_ENV'] ?? null;
}

if ('prod' !== $_SERVER['FORK_ENV']) {
    if (!class_exists(Dotenv::class)) {
        throw new RuntimeException('The "FORK_ENV" environment variable is not set to "prod". Please run "composer require symfony/dotenv" to load the ".env" files configuring the application.');
    }
    $dotenv = new Dotenv();

    $dotenv->load(__DIR__ . '/.env');

    // @TODO when updating to Fork 6, using Symfony 4, check if we still need to check the file's existence
    if (file_exists(__DIR__ . '/.env.local')) {
        $dotenv->load(__DIR__ . '/.env.local');
    }
}

$_SERVER['FORK_ENV'] = $_ENV['FORK_ENV'] = $_SERVER['APP_ENV'] = $_SERVER['FORK_ENV'] ?: $_ENV['FORK_ENV'] ?: 'dev';
$_SERVER['FORK_DEBUG'] = $_SERVER['FORK_DEBUG'] ?? $_ENV['FORK_DEBUG'] ?? 'prod' !== $_SERVER['FORK_ENV'];
$_SERVER['FORK_DEBUG'] = $_ENV['FORK_DEBUG'] = $_SERVER['APP_DEBUG'] = (int) $_SERVER['FORK_DEBUG'] || filter_var($_SERVER['FORK_DEBUG'], FILTER_VALIDATE_BOOLEAN) ? '1' : '0';

return $loader;
