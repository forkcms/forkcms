<?php

// use vendor generated autoloader
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/KernelLoader.php';

// Spoon is not autoloaded via Composer but uses its own old skool autoloader
set_include_path(__DIR__ . '/vendor/spoon/library' . PATH_SEPARATOR . get_include_path());
require_once 'spoon/spoon.php';
