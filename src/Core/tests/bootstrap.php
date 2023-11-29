<?php

use ForkCMS\Core\Domain\Kernel\Kernel;
use ForkCMS\Core\Domain\PDO\ForkConnection;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Dotenv\Dotenv;

$loader = require __DIR__ . '/../../../vendor/autoload.php';

(new Dotenv())->loadEnv(__DIR__ . '/../../../.env', null, 'test', ['test_install', 'test']);

if ($_ENV['APP_DEBUG']) {
    umask(0000);
}

function installTest()
{
    $kernel = new Kernel('test_install', true);
    $kernel->boot();

    $application = new Application($kernel);
    $application->setAutoExit(false);

    $application->run(
        new ArrayInput([
            'command' => 'cache:clear',
            '--no-warmup' => true,
        ])
    );

    fwrite(STDERR, print_r('Create test database', true));

    $application->run(
        new ArrayInput([
            'command' => 'doctrine:database:create',
            '--if-not-exists' => true,
        ])
    );

    fwrite(STDERR, print_r('Clear test database', true));
    $application->run(
        new ArrayInput([
            'command' => 'doctrine:schema:drop',
            '--full-database' => true,
            '--force' => true,
        ])
    );

    $application->run(
        new ArrayInput([
            'command' => 'forkcms:installer:install',
        ])
    );

    $kernel->shutdown();
}

try {
    $freshInstall = (($_ENV['TEST_DATABASE'] ?? 'cached') === 'fresh') || ForkConnection::get('test_instal')->exec('select 1 from backend__user') === false;
} catch (Throwable $throwable) {
    $freshInstall = true;
}
if ($freshInstall) {
    installTest();
}

return $loader;
